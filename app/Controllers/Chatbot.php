<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UnansweredQuestionModel;
use App\Models\AnsweredQuestionsModel;

class Chatbot extends Controller
{
    protected $dataset = [];
    protected $vocab = [];
    protected $tagCounts = [];
    protected $wordTagCounts = [];
    protected $tagAnswers = [];

    // Daftar kata kunci untuk respons khusus
    protected $keywordResponses = [
        'pagi' => 'Selamat pagi! Ada yang bisa saya bantu?',
        'siang' => 'Selamat siang! Ada yang bisa saya bantu?',
        'sore' => 'Selamat sore! Ada yang bisa saya bantu?',
        'malam' => 'Selamat malam! Ada yang bisa saya bantu?',
        'halo' => 'Halo! Ada yang bisa saya bantu?',
        'hai' => 'Hai! Ada yang bisa saya bantu hari ini?',
        'hi' => 'Hi! Ada yang bisa saya bantu?',
        'terima kasih' => 'Sama-sama! Senang bisa membantu Anda.',
        'makasih' => 'Sama-sama! Ada lagi yang bisa saya bantu?'
    ];

    // Daftar informasi default untuk pertanyaan yang tidak terjawab
    protected $defaultInformation = [
        'Saya tidak bisa menjawab pertanyaan anda, tetapi pertanyaan ini akan saya kirim ke admin. Mohon ditunggu admin akan menjawab segera mungkin.',
    ];

    // Threshold untuk menentukan kualitas jawaban
    protected $exactMatchThreshold = 100;      // Kecocokan hampir persis
    protected $goodSimilarityThreshold = 85; // Kemiripan yang baik
    protected $minimumSimilarityThreshold = 70; // Batas minimum untuk dianggap relevan
    protected $minimumConfidenceThreshold = 0.3; // Confidence minimum untuk Naive Bayes

    protected $unansweredQuestionModel;
    protected $answeredQuestionModel;

    public function __construct()
    {
        $this->unansweredQuestionModel = new UnansweredQuestionModel();
        $this->answeredQuestionModel = new AnsweredQuestionsModel();
        
        // Memuat dataset CSV
        $csvPath = FCPATH . 'dataset/chatbot_dataset.csv';

        if (file_exists($csvPath) && ($handle = fopen($csvPath, "r")) !== FALSE) {
            fgetcsv($handle); // Lewati header

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($data) >= 3) {
                    $question = strtolower(trim($data[0]));
                    $answer   = trim($data[1]);
                    $tag      = trim($data[2]);

                    $this->dataset[] = compact('question', 'answer', 'tag');

                    // Tokenisasi kata dari pertanyaan
                    $words = preg_split('/\s+/', preg_replace('/[^a-z0-9 ]/', '', $question));
                    $this->tagCounts[$tag] = ($this->tagCounts[$tag] ?? 0) + 1;

                    foreach ($words as $word) {
                        if ($word === '') continue;
                        $this->vocab[$word] = true;
                        $this->wordTagCounts[$tag][$word] = ($this->wordTagCounts[$tag][$word] ?? 0) + 1;
                    }

                    // Simpan jawaban berdasarkan tag
                    $this->tagAnswers[$tag][] = $answer;
                }
            }
            fclose($handle);
        }
    }

    public function index()
    {
        return view('chatbot');
    }

    public function getResponse()
    {
        $input = strtolower(trim($this->request->getPost('message')));

        if (!$input) {
            return $this->response->setJSON(['message' => 'Pertanyaan kosong.']);
        }

        // Log input untuk debugging
        log_message('info', 'Input user: ' . $input);

        // LANGKAH 1: Cek kata kunci sederhana (salam, terima kasih, dll)
        $keywordResponse = $this->checkKeywordResponses($input);
        if ($keywordResponse) {
            $this->saveToAnsweredQuestions($input, $keywordResponse, 'greeting');
            return $this->response->setJSON(['message' => $keywordResponse]);
        }

        // LANGKAH 2: Cek jawaban teknis yang sudah dijawab admin
        $technicalAnswer = $this->checkTechnicalAnswer($input);
        if ($technicalAnswer) {
            $this->saveToAnsweredQuestions($input, $technicalAnswer, 'technical');
            return $this->response->setJSON(['message' => $technicalAnswer]);
        }

        // LANGKAH 3: Cek kecocokan persis dalam dataset
        $exactMatch = $this->findExactMatch($input);
        if ($exactMatch) {
            $tag = $this->findTagFromExactMatch($input);
            $this->saveToAnsweredQuestions($input, $exactMatch, $tag);
            log_message('info', 'Exact match found: ' . $exactMatch);
            return $this->response->setJSON(['message' => $exactMatch]);
        }

        // LANGKAH 4: Analisis kemiripan dengan dataset
        $similarityResult = $this->findBestSimilarMatch($input);
        
        if ($similarityResult['score'] >= $this->exactMatchThreshold) {
            // Kecocokan hampir persis (95%+)
            $this->saveToAnsweredQuestions($input, $similarityResult['answer'], $similarityResult['tag']);
            log_message('info', 'Near exact match found with score: ' . $similarityResult['score']);
            return $this->response->setJSON(['message' => $similarityResult['answer']]);
        }
        
        if ($similarityResult['score'] >= $this->goodSimilarityThreshold) {
            // Kemiripan yang baik (75%+)
            $this->saveToAnsweredQuestions($input, $similarityResult['answer'], $similarityResult['tag']);
            log_message('info', 'Good similarity match found with score: ' . $similarityResult['score']);
            return $this->response->setJSON(['message' => $similarityResult['answer']]);
        }

        // LANGKAH 5: Gunakan Naive Bayes untuk pertanyaan yang lebih kompleks
        if (str_word_count($input) > 2) { // Hanya untuk pertanyaan yang tidak terlalu singkat
            $predictedTag = $this->predictTag($input);
            $tagConfidence = $this->calculateTagConfidence($input, $predictedTag);
            
            log_message('info', 'Predicted tag: ' . $predictedTag . ' with confidence: ' . $tagConfidence);
            
            if ($tagConfidence >= $this->minimumConfidenceThreshold) {
                $answer = $this->getAnswerByTag($predictedTag, $input);
                
                if ($answer && $answer !== "Maaf, saya belum mengerti maksud kamu.") {
                    $this->saveToAnsweredQuestions($input, $answer, $predictedTag);
                    log_message('info', 'Naive Bayes answer found: ' . $answer);
                    return $this->response->setJSON(['message' => $answer]);
                }
            }
        }

        // LANGKAH 6: Jika masih ada kemiripan minimal, berikan jawaban dengan disclaimer
        if ($similarityResult['score'] >= $this->minimumSimilarityThreshold) {
            $disclaimerAnswer = "Mungkin yang Anda maksud adalah: " . $similarityResult['answer'] . 
                              "\n\nJika ini tidak sesuai dengan yang Anda tanyakan, mohon ajukan pertanyaan dengan lebih spesifik.";
            
            $this->saveToAnsweredQuestions($input, $disclaimerAnswer, $similarityResult['tag']);
            log_message('info', 'Minimum similarity match with disclaimer');
            return $this->response->setJSON(['message' => $disclaimerAnswer]);
        }

        // LANGKAH 7: Fallback ke respons default
        log_message('info', 'No suitable answer found, using default response');
        $this->saveUnansweredQuestion($input);
        $defaultAnswer = $this->getInformationForUnknownQuery($input);
        
        return $this->response->setJSON(['message' => $defaultAnswer]);
    }

    private function checkKeywordResponses($input)
    {
        // Cek kecocokan persis terlebih dahulu
        if (isset($this->keywordResponses[$input])) {
            return $this->keywordResponses[$input];
        }

        // Cek apakah input mengandung kata kunci
        foreach ($this->keywordResponses as $keyword => $response) {
            if (strpos($input, $keyword) !== false) {
                return $response;
            }
        }

        return null;
    }

    private function findBestSimilarMatch($input)
    {
        $bestScore = 0;
        $bestAnswer = null;
        $bestTag = 'unknown';
        $bestQuestion = '';

        foreach ($this->dataset as $data) {
            // Gunakan multiple similarity algorithms untuk akurasi yang lebih baik
            $similarityScore = $this->calculateCombinedSimilarity($input, $data['question']);
            
            if ($similarityScore > $bestScore) {
                $bestScore = $similarityScore;
                $bestAnswer = $data['answer'];
                $bestTag = $data['tag'];
                $bestQuestion = $data['question'];
            }
        }

        log_message('info', "Best match: '{$bestQuestion}' with score: {$bestScore}");

        return [
            'score' => $bestScore,
            'answer' => $bestAnswer,
            'tag' => $bestTag,
            'question' => $bestQuestion
        ];
    }

    private function calculateCombinedSimilarity($input1, $input2)
    {
        // Kombinasi dari beberapa metode similarity
        $input1 = strtolower(trim($input1));
        $input2 = strtolower(trim($input2));

        // 1. Similar text percentage
        similar_text($input1, $input2, $similarTextPercent);

        // 2. Levenshtein distance similarity
        $maxLen = max(strlen($input1), strlen($input2));
        if ($maxLen == 0) return 0;
        $levenshteinSimilarity = (1 - levenshtein($input1, $input2) / $maxLen) * 100;

        // 3. Word overlap similarity
        $words1 = array_filter(explode(' ', $input1));
        $words2 = array_filter(explode(' ', $input2));
        $intersection = count(array_intersect($words1, $words2));
        $union = count(array_unique(array_merge($words1, $words2)));
        $wordOverlapSimilarity = $union > 0 ? ($intersection / $union) * 100 : 0;

        // Weighted combination
        $combinedScore = ($similarTextPercent * 0.4) + 
                        ($levenshteinSimilarity * 0.4) + 
                        ($wordOverlapSimilarity * 0.2);

        return $combinedScore;
    }

    private function checkTechnicalAnswer($input)
    {
        $result = $this->unansweredQuestionModel->findSimilarQuestion($input);
        
        if ($result['match'] && $result['score'] > $this->goodSimilarityThreshold) {
            return $result['match']['answer'];
        }
        
        return null;
    }

    private function calculateTagConfidence($input, $predictedTag)
    {
        $inputWords = preg_split('/\s+/', preg_replace('/[^a-z0-9 ]/', '', $input));
        $totalDocs = array_sum($this->tagCounts);
        $vocabSize = count($this->vocab);

        $scores = [];

        foreach ($this->tagCounts as $tag => $tagTotal) {
            // Prior probability P(tag)
            $tagProb = log($tagTotal / $totalDocs);

            // Likelihood untuk setiap kata
            $wordProb = 0;
            foreach ($inputWords as $word) {
                if ($word === '') continue;

                $wordCount = $this->wordTagCounts[$tag][$word] ?? 0;
                $totalWordsInTag = array_sum($this->wordTagCounts[$tag] ?? []);

                // Laplace smoothing
                $wordProb += log(($wordCount + 1) / ($totalWordsInTag + $vocabSize));
            }

            $scores[$tag] = $tagProb + $wordProb;
        }

        // Convert to probabilities
        $maxScore = max($scores);
        $expScores = [];
        $totalExp = 0;

        foreach ($scores as $tag => $score) {
            $expScore = exp($score - $maxScore); // Normalisasi untuk mencegah overflow
            $expScores[$tag] = $expScore;
            $totalExp += $expScore;
        }

        // Confidence adalah probabilitas relatif dari tag yang diprediksi
        return isset($expScores[$predictedTag]) && $totalExp > 0 ? 
               $expScores[$predictedTag] / $totalExp : 0;
    }

    private function findExactMatch($input)
    {
        foreach ($this->dataset as $data) {
            if (strtolower(trim($data['question'])) === $input) {
                return $data['answer'];
            }
        }
        return null;
    }

    private function findTagFromExactMatch($input)
    {
        foreach ($this->dataset as $data) {
            if (strtolower(trim($data['question'])) === $input) {
                return $data['tag'];
            }
        }
        return 'unknown';
    }

    private function predictTag($input)
    {
        $inputWords = preg_split('/\s+/', preg_replace('/[^a-z0-9 ]/', '', $input));
        $totalDocs = array_sum($this->tagCounts);
        $vocabSize = count($this->vocab);

        $scores = [];

        foreach ($this->tagCounts as $tag => $tagTotal) {
            $logProb = log($tagTotal / $totalDocs);

            foreach ($inputWords as $word) {
                if ($word === '') continue;

                $wordCount = $this->wordTagCounts[$tag][$word] ?? 0;
                $totalWordsInTag = array_sum($this->wordTagCounts[$tag] ?? []);

                // Laplace smoothing
                $logProb += log(($wordCount + 1) / ($totalWordsInTag + $vocabSize));
            }

            $scores[$tag] = $logProb;
        }

        arsort($scores);
        return array_key_first($scores);
    }

    private function getAnswerByTag($tag, $input)
    {
        if (!isset($this->tagAnswers[$tag])) {
            return "Maaf, saya belum mengerti maksud kamu.";
        }

        // Cari jawaban terbaik dalam tag yang sama
        $bestScore = 0;
        $bestAnswer = null;

        foreach ($this->dataset as $data) {
            if ($data['tag'] === $tag) {
                $similarity = $this->calculateCombinedSimilarity($input, $data['question']);
                if ($similarity > $bestScore) {
                    $bestScore = $similarity;
                    $bestAnswer = $data['answer'];
                }
            }
        }

        // Jika ada jawaban dengan skor yang cukup baik, gunakan itu
        if ($bestAnswer && $bestScore > 30) {
            return $bestAnswer;
        }

        // Fallback ke jawaban acak dari tag
        return $this->tagAnswers[$tag][array_rand($this->tagAnswers[$tag])];
    }

    private function getInformationForUnknownQuery($input)
    {
        $randomInfo = $this->defaultInformation[array_rand($this->defaultInformation)];
        return "Mohon maaf, " . $randomInfo;
    }

    private function saveUnansweredQuestion($question)
    {
        $existingQuestion = $this->unansweredQuestionModel
            ->where('question', $question)
            ->first();
            
        if (!$existingQuestion) {
            $this->unansweredQuestionModel->save([
                'question' => $question,
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            log_message('info', 'Pertanyaan baru disimpan ke database: ' . $question);
        }
    }

    private function saveToAnsweredQuestions($question, $answer, $tag)
    {
        $unansweredQuestion = $this->unansweredQuestionModel
            ->where('question', $question)
            ->first();
            
        if ($unansweredQuestion && !empty($unansweredQuestion['tag'])) {
            $tag = $unansweredQuestion['tag'];
        }
        
        if (empty($tag)) {
            $tag = 'general';
        }
        
        $existingQuestion = $this->answeredQuestionModel
            ->where('question', $question)
            ->first();
            
        if ($existingQuestion) {
            $this->answeredQuestionModel->update($existingQuestion['id'], [
                'answer' => $answer,
                'tag' => $tag,
                'frequency' => $existingQuestion['frequency'] + 1,
                'last_asked_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            $this->answeredQuestionModel->save([
                'question' => $question,
                'answer' => $answer,
                'tag' => $tag,
                'frequency' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'last_asked_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        if ($unansweredQuestion) {
            $this->unansweredQuestionModel->update($unansweredQuestion['id'], [
                'status' => 'answered',
                'answer' => $answer
            ]);
        }
    }

    public function moveAnsweredQuestionToAnotherTable($question, $answer, $tag)
    {
        return $this->saveToAnsweredQuestions($question, $answer, $tag);
    }

    public function showFrequentQuestions($tag = null)
    {
        $frequentQuestions = $this->answeredQuestionModel->getQuestionsByTag($tag);

        return view('frequent_questions', [
            'frequentQuestions' => $frequentQuestions,
            'selectedTag' => $tag
        ]);
    }
}
