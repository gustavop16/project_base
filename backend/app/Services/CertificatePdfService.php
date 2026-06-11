<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\CertificateFormResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class CertificatePdfService
{
    public function generate(Certificate $certificate): string
    {
        $certificate->load([
            'vessel',
            'certificateForm',
            'createdBy:id,name',
            'approvedBy:id,name',
        ]);

        $response = CertificateFormResponse::with(['answers.question', 'user:id,name'])
            ->where('certificate_id', $certificate->id)
            ->latest()
            ->first();

        $verifyUrl = rtrim(env('FRONTEND_URL', config('app.url')), '/')
            . '/certificados/verificar/' . $certificate->token;

        $qrWriter = new PngWriter();
        $qrResult = $qrWriter->write(QrCode::create($verifyUrl)->setSize(160)->setMargin(4));
        $qrCode   = 'data:image/png;base64,' . base64_encode($qrResult->getString());

        $pdf = Pdf::loadView('pdf.certificate', compact(
            'certificate',
            'response',
            'qrCode',
            'verifyUrl'
        ))->setPaper('a4', 'portrait');

        $filename = 'certificate_' . $certificate->id . '_' . time() . '.pdf';
        $path     = 'certificates/' . $filename;

        Storage::put($path, $pdf->output());

        return $path;
    }
}
