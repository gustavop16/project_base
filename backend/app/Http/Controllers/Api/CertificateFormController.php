<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CertificateFormRequest;
use App\Http\Resources\CertificateFormResource;
use App\Models\CertificateForm;

class CertificateFormController extends Controller
{
    public function __construct(private CertificateForm $model) {}

    public function index()
    {
        $forms = $this->model->withCount('questions')->get();
        return CertificateFormResource::collection($forms);
    }

    public function store(CertificateFormRequest $request)
    {
        $form = $this->model->create($request->safe()->except('questions'));
        $this->syncQuestions($form, $request->input('questions', []));
        return new CertificateFormResource($form->load('questions'));
    }

    public function show(CertificateForm $certificateForm)
    {
        return new CertificateFormResource($certificateForm->load('questions'));
    }

    public function update(CertificateFormRequest $request, CertificateForm $certificateForm)
    {
        $certificateForm->update($request->safe()->except('questions'));
        $this->syncQuestions($certificateForm, $request->input('questions', []));
        return new CertificateFormResource($certificateForm->load('questions'));
    }

    public function destroy(CertificateForm $certificateForm)
    {
        $certificateForm->delete();
        return response()->json(['message' => 'ok'], 200);
    }

    private function syncQuestions(CertificateForm $form, array $questions): void
    {
        $sync = [];
        foreach ($questions as $index => $q) {
            $sync[$q['id']] = ['order' => $index + 1];
        }
        $form->questions()->sync($sync);
    }
}
