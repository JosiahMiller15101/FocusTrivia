<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\QuestionSubmission;
use App\Models\Question;

class BackfillQuestionSubmissionCodes extends Command
{
    protected $signature = 'trivia:backfill-question-codes';
    protected $description = 'Backfill question_code in question_submissions based on question_id';

    public function handle()
    {
        $count = 0;
        QuestionSubmission::whereNull('question_code')->orWhere('question_code', '')->chunk(100, function ($submissions) use (&$count) {
            foreach ($submissions as $submission) {
                $question = Question::find($submission->question_id);
                if ($question && $question->code) {
                    $submission->question_code = $question->code;
                    $submission->save();
                    $count++;
                }
            }
        });
        $this->info("Backfilled $count question_submissions.");
    }
}
