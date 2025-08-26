<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UnansweredQuestionModel;
use App\Models\AnsweredQuestionsModel;

class Chatbot extends Controller
{
    protected $dataset = [];
    protected $keywordResponses = [
        'pagi' => 'Selamat pagi! Ada yang bisa saya bantu?',
        'siang' => 'Selamat siang! Ada yang bisa saya bantu?',
        'sore' => 'Selamat sore! Ada yang bisa saya bantu?',
        'malam' => 'Selamat malam! Ada yang bisa saya bantu?',
        'halo' => 'Halo! Ada yang bisa saya bantu?',
        'hai' => 'Hai! Ada yang bisa saya bantu hari ini?',
        'hi' => 'Hi! Ada yang bisa saya bantu?'
    ];
    protected $defaultInformation = [
        'Saya tidak bisa menjawab pertanyaan anda, tetapi pertanyaan ini akan saya kirim ke admin. Mohon ditunggu admin akan menjawab segera mungkin.',
    ];

    protected $unansweredQuestionModel;
    protected $answeredQuestionModel;

    public function __construct()
    {
        $this->unansweredQuestionModel = new UnansweredQuestionModel();
        $this->answeredQuestionModel   = new AnsweredQuestionsModel();

        // Load dataset CSV
        $csvPath = FCPATH . 'dataset/chatbot_dataset.csv';
        if (file_exists($csvPath) && ($handle = fopen($csvPath, "r")) !== FALSE) {
            fgetcsv($handle);
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($data) >= 2) {
                    $this->dataset[] = [
                        'question' => strtolower(trim($data[0])),
                        'answer'   => trim($data[1])
                    ];
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

        // 1. Greeting
        foreach ($this->keywordResponses as $keyword => $response) {
            if (strpos($input, $keyword) !== false) {
                $this->saveToAnsweredQuestions($input, $response, 'greeting');
                return $this->response->setJSON(['message' => $response]);
            }
        }

        // 2. Cek database answered_questions
        $existingAnswer = $this->answeredQuestionModel->where('question', $input)->first();
        if ($existingAnswer) {
            return $this->response->setJSON(['message' => $existingAnswer['answer']]);
        }

        // 3. Cek dataset (exact)
        foreach ($this->dataset as $data) {
            if ($data['question'] === $input) {
                $this->saveToAnsweredQuestions($input, $data['answer'], 'dataset');
                return $this->response->setJSON(['message' => $data['answer']]);
            }
        }

        // 3b. Cek dataset (similar)
        $similar = $this->findSimilarMatch($input);
        if ($similar['score'] > 80) {
            $this->saveToAnsweredQuestions($input, $similar['answer'], 'dataset-similar');
            return $this->response->setJSON(['message' => $similar['answer']]);
        }

        // 4. Coba tanya ke Gemini AI
        $geminiAnswer = $this->askGeminiAI($input);
        if ($geminiAnswer && $geminiAnswer['confidence'] > 0.7) {
            $this->saveToAnsweredQuestions($input, $geminiAnswer['text'], 'gemini');
            return $this->response->setJSON(['message' => $geminiAnswer['text']]);
        }

        // 5. Fallback ke Admin
        $this->saveUnansweredQuestion($input);
        $answer = $this->getInformationForUnknownQuery();
        return $this->response->setJSON(['message' => $answer]);
    }

    private function findSimilarMatch($input)
    {
        $bestScore = 0;
        $bestAnswer = null;

        foreach ($this->dataset as $data) {
            similar_text($input, $data['question'], $percent);
            if ($percent > $bestScore) {
                $bestScore = $percent;
                $bestAnswer = $data['answer'];
            }
        }

        return [
            'score' => $bestScore,
            'answer' => $bestAnswer
        ];
    }

    private function askGeminiAI($input)
    {
        $apiKey = getenv('GEMINI_API_KEY');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateText?key=" . $apiKey;

        $payload = [
            'prompt' => ['text' => $input],
        ];

        $options = [
            'http' => [
                'header'  => "Content-Type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($payload)
            ]
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === FALSE) return null;

        $response = json_decode($result, true);

        if (!empty($response['candidates'][0]['output'])) {
            return [
                'text' => $response['candidates'][0]['output'],
                'confidence' => 0.9 // untuk demo, bisa diganti logika scoring
            ];
        }

        return null;
    }

    private function getInformationForUnknownQuery()
    {
        return $this->defaultInformation[array_rand($this->defaultInformation)];
    }

    private function saveUnansweredQuestion($question)
    {
        $exists = $this->unansweredQuestionModel->where('question', $question)->first();
        if (!$exists) {
            $this->unansweredQuestionModel->save([
                'question' => $question,
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    private function saveToAnsweredQuestions($question, $answer, $tag)
    {
        $exists = $this->answeredQuestionModel->where('question', $question)->first();

        if ($exists) {
            $this->answeredQuestionModel->update($exists['id'], [
                'answer' => $answer,
                'tag' => $tag,
                'frequency' => $exists['frequency'] + 1,
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
    }
}
