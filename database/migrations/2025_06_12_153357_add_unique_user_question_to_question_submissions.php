<?php

namespace Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('question_submissions', function (Blueprint $table) {
            $table->unique(['user_id', 'question_id'], 'unique_user_question');
        });
    }

    public function down()
    {
        Schema::table('question_submissions', function (Blueprint $table) {
            $table->dropUnique('unique_user_question');
        });
    }
};

