<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CertificateRequest;
use App\Http\Resources\CertificateResource;
use App\Models\Certificate;
use App\Models\CertificateFormResponse;
use App\Models\User;
use App\Notifications\CertificateApprovedNotification;
use App\Notifications\CertificateFilledNotification;
use App\Notifications\CertificateRejectedNotification;
use App\Notifications\CertificateRequestedNotification;
use App\Services\CertificatePdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user  = auth()->user();
        $query = Certificate::with('certificateForm:id,name', 'vessel:id,name', 'createdBy:id,name')
            ->latest();

        if (!$user->hasRole('admin')) {
            $ownVesselIds = \App\Models\Vessel::where('created_by', $user->id)->pluck('id');

            $query->where(function ($q) use ($user, $ownVesselIds) {
                $q->where('created_by', $user->id)
                  ->orWhereIn('vessel_id', $ownVesselIds);
            });
        }

        return CertificateResource::collection($query->get());
    }

    public function store(CertificateRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $certificate = Certificate::create([
            'certificate_form_id' => $request->certificate_form_id,
            'vessel_id'           => $request->vessel_id,
            'token'               => Str::uuid()->toString(),
            'status'              => 'pending',
            'monitoring_start'    => $request->monitoring_start,
            'monitoring_end'      => $request->monitoring_end,
            'created_by'          => $user->id,
        ]);

        $certificate->load('certificateForm', 'vessel', 'createdBy');

        $recipients = $this->vesselRespondents($certificate->vessel_id)
            ->where('id', '!=', $user->id);

        Notification::send($recipients, new CertificateRequestedNotification($certificate));

        return new CertificateResource($certificate);
    }

    public function show(Certificate $certificate)
    {
        return new CertificateResource(
            $certificate->load('certificateForm', 'vessel', 'createdBy')
        );
    }

    public function destroy(Certificate $certificate)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return response()->json(['message' => 'Administradores não podem excluir certificados.'], 403);
        }

        $certificate->delete();
        return response()->json(['message' => 'ok'], 200);
    }

    public function getForm(Certificate $certificate)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return response()->json(['message' => 'Administradores não podem responder formulários.'], 403);
        }

        if ($certificate->status !== 'pending') {
            return response()->json(['message' => 'Este certificado não pode ser respondido.'], 422);
        }

        $canRespond = $certificate->created_by === $user->id
            || $this->userBelongsToVessel($user->id, $certificate->vessel_id);

        if (! $canRespond) {
            return response()->json(['message' => 'Você não tem permissão para responder este formulário.'], 403);
        }

        $certificate->load(['certificateForm.questions', 'vessel']);
        $form = $certificate->certificateForm;

        return response()->json([
            'data' => [
                'id'              => $certificate->id,
                'status'          => $certificate->status,
                'rejected_reason' => $certificate->rejected_reason,
                'vessel'          => [
                    'id'               => $certificate->vessel->id,
                    'name'             => $certificate->vessel->name,
                    'imo_number'       => $certificate->vessel->imo_number,
                    'call_sign'        => $certificate->vessel->call_sign,
                    'port_of_registry' => $certificate->vessel->port_of_registry,
                    'gross_tonnage'    => $certificate->vessel->gross_tonnage,
                    'built_at'         => $certificate->vessel->built_at?->format('d/m/Y'),
                ],
                'form' => [
                    'id'          => $form->id,
                    'name'        => $form->name,
                    'description' => $form->description,
                    'questions'   => $form->questions->map(fn($q) => [
                        'id'          => $q->id,
                        'order'       => $q->pivot->order,
                        'question'    => $q->question,
                        'description' => $q->description,
                        'score'       => $q->score,
                        'input_type'  => $q->input_type,
                        'options'     => $q->options ?? [],
                    ])->values(),
                ],
            ],
        ]);
    }

    public function submitResponse(Request $request, Certificate $certificate)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return response()->json(['message' => 'Administradores não podem responder formulários.'], 403);
        }

        $canRespond = $certificate->created_by === $user->id
            || $this->userBelongsToVessel($user->id, $certificate->vessel_id);

        if (! $canRespond) {
            return response()->json(['message' => 'Você não tem permissão para responder este formulário.'], 403);
        }

        $request->validate([
            'answers'      => 'required|string',
            'observations' => 'nullable|string',
            'files'        => 'nullable|array',
            'files.*'      => 'file|max:20480',
        ]);

        $answers = json_decode($request->input('answers'), true);

        if (! is_array($answers)) {
            return response()->json(['message' => 'Formato de respostas inválido.'], 422);
        }

        $response = CertificateFormResponse::create([
            'certificate_form_id' => $certificate->certificate_form_id,
            'certificate_id'      => $certificate->id,
            'user_id'             => $user->id,
            'status'              => 'completed',
            'observations'        => $request->input('observations'),
        ]);

        foreach ($answers as $item) {
            $answer  = $item['answer'] ?? null;
            $fileKey = 'files.' . $item['question_id'];

            if ($request->hasFile($fileKey)) {
                $path   = $request->file($fileKey)->store('certificate-answers', 'public');
                $answer = basename($path);
            }

            $response->answers()->create([
                'question_id'     => $item['question_id'],
                'answer'          => $answer,
                'text_complement' => $item['text_complement'] ?? null,
            ]);
        }

        $certificate->update([
            'status'          => 'completed',
            'rejected_reason' => null,
            'rejected_by'     => null,
            'rejected_at'     => null,
        ]);

        $certificate->load('certificateForm', 'vessel');
        $admins = User::role('admin')->get();
        Notification::send($admins, new CertificateFilledNotification($certificate, $user));

        return response()->json(['message' => 'ok'], 201);
    }

    public function history(Certificate $certificate)
    {
        $certificate->load(['createdBy:id,name', 'approvedBy:id,name', 'rejectedBy:id,name']);
        $responses = $certificate->responses()->with('user:id,name')->orderBy('created_at')->get();

        $events = collect();

        $events->push([
            'type'  => 'requested',
            'date'  => $certificate->created_at,
            'user'  => $certificate->createdBy
                ? ['id' => $certificate->createdBy->id, 'name' => $certificate->createdBy->name]
                : null,
            'notes' => null,
        ]);

        foreach ($responses as $response) {
            $events->push([
                'type'  => 'filled',
                'date'  => $response->created_at,
                'user'  => $response->user
                    ? ['id' => $response->user->id, 'name' => $response->user->name]
                    : null,
                'notes' => $response->observations ?: null,
            ]);
        }

        if ($certificate->rejected_at) {
            $events->push([
                'type'  => 'rejected',
                'date'  => $certificate->rejected_at,
                'user'  => $certificate->rejectedBy
                    ? ['id' => $certificate->rejectedBy->id, 'name' => $certificate->rejectedBy->name]
                    : null,
                'notes' => $certificate->rejected_reason,
            ]);
        }

        if ($certificate->approved_at) {
            $events->push([
                'type'  => 'approved',
                'date'  => $certificate->approved_at,
                'user'  => $certificate->approvedBy
                    ? ['id' => $certificate->approvedBy->id, 'name' => $certificate->approvedBy->name]
                    : null,
                'notes' => null,
            ]);
        }

        return response()->json([
            'data' => $events->sortBy('date')->values(),
        ]);
    }

    public function getResponse(Certificate $certificate)
    {
        $response = $certificate->responses()->with('answers', 'user')->latest()->first();

        if (! $response) {
            return response()->json(['message' => 'Nenhuma resposta encontrada.'], 404);
        }

        $certificate->load(['certificateForm.questions', 'vessel']);
        $form       = $certificate->certificateForm;
        $answersMap = $response->answers->keyBy('question_id');

        return response()->json([
            'data' => [
                'id'           => $response->id,
                'observations' => $response->observations,
                'user'         => $response->user
                    ? ['id' => $response->user->id, 'name' => $response->user->name]
                    : null,
                'submitted_at' => $response->created_at,
                'vessel'       => [
                    'id'   => $certificate->vessel->id,
                    'name' => $certificate->vessel->name,
                ],
                'form' => [
                    'id'        => $form->id,
                    'name'      => $form->name,
                    'questions' => $form->questions->map(fn($q) => [
                        'id'              => $q->id,
                        'question'        => $q->question,
                        'description'     => $q->description,
                        'input_type'      => $q->input_type,
                        'score'           => $q->score,
                        'options'         => $q->options ?? [],
                        'answer'          => $answersMap->get($q->id)?->answer,
                        'text_complement' => $answersMap->get($q->id)?->text_complement,
                        'file_url'        => ($q->input_type === 'upload' && $answersMap->get($q->id)?->answer)
                            ? asset('storage/certificate-answers/' . $answersMap->get($q->id)->answer)
                            : null,
                    ])->values(),
                ],
            ],
        ]);
    }

    public function approve(Certificate $certificate, CertificatePdfService $pdfService)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($certificate->status !== 'completed') {
            return response()->json(['message' => 'Certificate is not completed yet.'], 422);
        }

        $pdfPath = $pdfService->generate($certificate);

        $certificate->update([
            'status'      => 'approved',
            'pdf_path'    => $pdfPath,
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        $certificate->load('certificateForm', 'vessel', 'createdBy', 'approvedBy');

        $filler     = $certificate->responses()->with('user')->latest()->first()?->user;
        $recipients = collect([$certificate->createdBy, $filler])->filter()->unique('id');
        Notification::send($recipients, new CertificateApprovedNotification($certificate));

        return new CertificateResource($certificate);
    }

    public function reject(Request $request, Certificate $certificate)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $request->validate([
            'reason' => 'required|string|max:2000',
        ]);

        if ($certificate->status !== 'completed') {
            return response()->json(['message' => 'Apenas certificados aguardando aprovação podem ser reprovados.'], 422);
        }

        $certificate->update([
            'status'          => 'rejected',
            'rejected_reason' => $request->reason,
            'rejected_by'     => $user->id,
            'rejected_at'     => now(),
        ]);

        $certificate->load('certificateForm', 'vessel', 'createdBy', 'rejectedBy');

        $filler     = $certificate->responses()->with('user')->latest()->first()?->user;
        $recipients = collect([$certificate->createdBy, $filler])->filter()->unique('id');
        Notification::send($recipients, new CertificateRejectedNotification($certificate));

        return new CertificateResource($certificate);
    }

    public function downloadPdf(Certificate $certificate)
    {
        if (! $certificate->pdf_path || ! Storage::exists($certificate->pdf_path)) {
            return response()->json(['message' => 'PDF not found.'], 404);
        }

        return Storage::download(
            $certificate->pdf_path,
            'certificado_' . str_pad($certificate->id, 6, '0', STR_PAD_LEFT) . '.pdf'
        );
    }

    public function verify(string $token)
    {
        $certificate = Certificate::with('certificateForm:id,name', 'vessel:id,name', 'approvedBy:id,name')
            ->where('token', $token)
            ->where('status', 'approved')
            ->firstOrFail();

        return response()->json([
            'data' => [
                'id'               => $certificate->id,
                'token'            => $certificate->token,
                'form_name'        => $certificate->certificateForm->name,
                'vessel_name'      => $certificate->vessel->name,
                'approved_by'      => $certificate->approvedBy?->name,
                'approved_at'      => $certificate->approved_at,
                'monitoring_start' => $certificate->monitoring_start?->format('Y-m-d'),
                'monitoring_end'   => $certificate->monitoring_end?->format('Y-m-d'),
            ],
        ]);
    }

    private function userBelongsToVessel(int $userId, int $vesselId): bool
    {
        $isCreator    = \App\Models\Vessel::where('id', $vesselId)->where('created_by', $userId)->exists();
        $isAssociated = DB::table('user_vessel')->where('user_id', $userId)->where('vessel_id', $vesselId)->exists();

        return $isCreator || $isAssociated;
    }

    /** Returns all users who can respond to certificates for the given vessel. */
    private function vesselRespondents(int $vesselId): \Illuminate\Database\Eloquent\Collection
    {
        $creatorIds      = \App\Models\Vessel::where('id', $vesselId)->pluck('created_by')->filter();
        $associatedIds   = DB::table('user_vessel')->where('vessel_id', $vesselId)->pluck('user_id');
        $recipientIds    = $creatorIds->merge($associatedIds)->unique()->values();

        return User::whereIn('id', $recipientIds)->get();
    }
}
