<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Question;

class TriviaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $response = Http::get('https://opentdb.com/api.php?amount=30&category=9&difficulty=easy&type=multiple');

        $data = $response->json();

        foreach ($data['results'] as $item) {
            Question::create([
                'category' => $item['category'],
                'type' => $item['type'],
                'difficulty' => $item['difficulty'],
                'question' => html_entity_decode($item['question']),
                'correct_answer' => html_entity_decode($item['correct_answer']),
                'incorrect_answers' => json_encode(array_map('html_entity_decode', $item['incorrect_answers'])),
            ]);
        }
    }
}
