<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CertificateFormResponseRequest;
use App\Http\Resources\CertificateFormResponseResource;
use App\Models\CertificateFormResponse;

class CertificateFormResponseController extends Controller
{
    public function __construct(private CertificateFormResponse $model) {}

    public function index()
    {
        $responses = $this->model
            ->with('certificateForm:id,name', 'user:id,name')
            ->withCount('answers')
            ->latest()
            ->get();
        return CertificateFormResponseResource::collection($responses);
    }

    public function store(CertificateFormResponseRequest $request)
    {
        $response = $this->model->create([
            'certificate_form_id' => $request->certificate_form_id,
            'user_id'             => auth()->id(),
            'status'              => 'completed',
            'observations'        => $request->observations,
        ]);

        foreach ($request->answers as $item) {
            $response->answers()->create([
                'question_id'     => $item['question_id'],
                'answer'          => $item['answer'] ?? null,
                'text_complement' => $item['text_complement'] ?? null,
            ]);
        }

        return new CertificateFormResponseResource(
            $response->load('answers', 'certificateForm', 'user')
        );
    }

    public function show(CertificateFormResponse $certificateFormResponse)
    {
        return new CertificateFormResponseResource(
            $certificateFormResponse->load('answers', 'certificateForm', 'user')
        );
    }
}
