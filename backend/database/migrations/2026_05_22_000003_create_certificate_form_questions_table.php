<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_form_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certificate_form_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('order')->default(0);
            $table->unique(['certificate_form_id', 'question_id'], 'cfq_form_question_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_form_questions');
    }
};
