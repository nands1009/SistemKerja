<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UnansweredQuestionModel;
use App\Models\AnsweredQuestionsModel;
use Exception;

class Chatbot extends Controller
{
    protected $dataset = [];
    protected $vocab = [];
    protected $tagCounts = [];
    protected $wordTagCounts = [];
    protected $tagAnswers = [];
    protected $geminiApiKey;

    // Respon sapaan sederhana (biar natural, tidak selalu ke AI)
    protected $keywordResponses = [
        'pagi'  => 'Selamat pagi, ada yang bisa saya bantu terkait laporan atau kinerja Anda?',
        'siang' => 'Selamat siang, apa ada kendala pada sistem kinerja?',
        'sore'  => 'Selamat sore, apakah Anda ingin melihat laporan atau informasi lain?',
        'malam' => 'Selamat malam, apakah ada hal terkait pekerjaan yang ingin Anda tanyakan?',
        'halo'  => 'Halo! Ada yang bisa saya bantu?',
        'hai'   => 'Hai, bagaimana saya bisa membantu Anda?',
        'hi'    => 'Hi! Apa yang ingin Anda tanyakan hari ini?'
    ];

    protected $defaultInformation = [
        'Saya sedang mencarikan jawaban yang sesuai, mohon tunggu sebentar...',
        'Baik, saya akan bantu mencarikan informasinya.',
        'Saya akan coba bantu mencari jawaban yang relevan.'
    ];

    protected $unansweredQuestionModel;
    protected $answeredQuestionModel;

    public function __construct()
    {
        $this->geminiApiKey = getenv('GEMINI_API_KEY') ?: 'YOUR_GEMINI_API_KEY_HERE';
        $this->unansweredQuestionModel = new UnansweredQuestionModel();
        $this->answeredQuestionModel = new AnsweredQuestionsModel();

        // Load dataset CSV
        $csvPath = FCPATH . 'dataset/chatbot_dataset.csv';
        if (file_exists($csvPath) && ($handle = fopen($csvPath, "r")) !== FALSE) {
            fgetcsv($handle); // skip header
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($data) >= 3) {
                    $question = strtolower(trim($data[0]));
                    $answer   = trim($data[1]);
                    $tag      = trim($data[2]);

                    $this->dataset[] = compact('question', 'answer', 'tag');

                    $words = preg_split('/\s+/', preg_replace('/[^a-z0-9 ]/', '', $question));
                    $this->tagCounts[$tag] = ($this->tagCounts[$tag] ?? 0) + 1;
                    foreach ($words as $word) {
                        if ($word === '') continue;
                        $this->vocab[$word] = true;
                        $this->wordTagCounts[$tag][$word] = ($this->wordTagCounts[$tag][$word] ?? 0) + 1;
                    }
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

        if (!$input || strlen($input) < 2) {
            return $this->response->setJSON(['message' => 'Silakan ajukan pertanyaan yang lebih jelas.']);
        }

        // 1. Cek sapaan
        foreach ($this->keywordResponses as $keyword => $response) {
            if (strpos($input, $keyword) !== false) {
                $this->saveToAnsweredQuestions($input, $response, 'greeting');
                return $this->response->setJSON(['message' => $response]);
            }
        }

        // 2. Cek apakah sudah ada jawaban manual
        $technicalAnswer = $this->checkTechnicalAnswer($input);
        if ($technicalAnswer) {
            $this->saveToAnsweredQuestions($input, $technicalAnswer, 'technical');
            return $this->response->setJSON(['message' => $technicalAnswer]);
        }

        // 3. Exact match di dataset
        $exactMatch = $this->findExactMatch($input);
        if ($exactMatch) {
            $tag = $this->findTagFromExactMatch($input);
            $this->saveToAnsweredQuestions($input, $exactMatch, $tag);
            return $this->response->setJSON(['message' => $exactMatch]);
        }

        // 4. Similar match
        $similarMatch = $this->findSimilarMatch($input);
        if ($similarMatch && $similarMatch['score'] > 90) {
            $this->saveToAnsweredQuestions($input, $similarMatch['answer'], $similarMatch['tag']);
            return $this->response->setJSON(['message' => $similarMatch['answer']]);
        }

        // 5. Fallback Naive Bayes
        $predictedTag = $this->predictTag($input);
        $confidence = $this->calculateTagConfidence($input, $predictedTag);

        if ($confidence >= 0.3) {
            $answer = $this->getAnswerByTag($predictedTag, $input);
            $this->saveToAnsweredQuestions($input, $answer, $predictedTag);
            return $this->response->setJSON(['message' => $answer]);
        }

        // 6. Jika semua gagal → AI fallback
        $answer = $this->tryGeminiFallback($input);
        return $this->response->setJSON(['message' => $answer]);
    }

    // === HELPER AI ===
    private function tryGeminiFallback($input)
    {
        $geminiAnswer = $this->getGeminiResponse($input);
        if ($geminiAnswer && $geminiAnswer !== 'error') {
            $this->saveToAnsweredQuestions($input, $geminiAnswer, 'ai_generated');
            return $geminiAnswer;
        } else {
            $this->saveUnansweredQuestion($input);
            return $this->getInformationForUnknownQuery($input);
        }
    }

    private function getGeminiResponse($question)
    {
        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=" . $this->geminiApiKey;

            // Prompt natural agar tidak terlihat "AI"
            $prompt = "Anda adalah asisten virtual internal perusahaan. 
            Jawab dalam bahasa Indonesia dengan gaya profesional, ringkas, dan jelas. 
            Fokus pada hal terkait kinerja pegawai, laporan kerja, absensi, dan fitur sistem. 
            Jangan menyebutkan Anda AI. 
            
            Pertanyaan: $question";

            $data = [
                'contents' => [[ 'parts' => [['text' => $prompt]] ]],
                'generationConfig' => [
                    'temperature' => 0.6,
                    'maxOutputTokens' => 250,
                    'topP' => 0.8,
                    'topK' => 40
                ]
            ];

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_TIMEOUT => 30,
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                log_message('error', "Gemini API error $httpCode: $response");
                return 'error';
            }

            $result = json_decode($response, true);

            if (!empty($result['candidates'][0]['content']['parts'])) {
                foreach ($result['candidates'][0]['content']['parts'] as $part) {
                    if (!empty($part['text'])) {
                        return trim($part['text']);
                    }
                }
            }
            return 'error';
        } catch (Exception $e) {
            log_message('error', 'Gemini exception: ' . $e->getMessage());
            return 'error';
        }
    }

    // === fungsi lain (findExactMatch, findSimilarMatch, predictTag, dll) tetap sama ===
}
