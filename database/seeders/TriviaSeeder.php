<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Question;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TriviaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('questions'); 

        // First request: 50 questions
        $response1 = Http::get('https://the-trivia-api.com/v2/questions?limit=50&difficulties=easy,medium&type=multiple&region=US');
        $data1 = $response1->json();

        // Second request: 10 questions
        $response2 = Http::get('https://the-trivia-api.com/v2/questions?limit=10&difficulties=easy,medium&type=multiple&region=US');
        $data2 = $response2->json();

        // Merge both arrays
        $allQuestions = array_merge($data1, $data2);

        // Insert all questions
        foreach ($allQuestions as $item) {
            $code = Str::uuid()->toString();
            Question::create([
                'category' => $item['category'] ?? 'General',
                'type' => 'multiple',
                'difficulty' => $item['difficulty'] ?? 'medium',
                'question' => html_entity_decode($item['question']['text']),
                'correct_answer' => html_entity_decode($item['correctAnswer']),
                'incorrect_answers' => json_encode(array_map('html_entity_decode', $item['incorrectAnswers'])),
                'code' => $code,
            ]);
        }
    }
}
