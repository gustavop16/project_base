<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuestionRequest;
use App\Http\Resources\QuestionResource;
use App\Models\Question;

class QuestionController extends Controller
{
    public function __construct(private Question $question_model) {}

    public function index()
    {
        $questions = $this->question_model->get();
        return QuestionResource::collection($questions);
    }

    public function store(QuestionRequest $request)
    {
        $question = $this->question_model->create($request->validated());
        return new QuestionResource($question);
    }

    public function show(Question $question)
    {
        return new QuestionResource($question);
    }

    public function update(QuestionRequest $request, Question $question)
    {
        $question->update($request->validated());
        return new QuestionResource($question);
    }

    public function destroy(Question $question)
    {
        $question->delete();
        return response()->json(['message' => 'ok'], 200);
    }
}
