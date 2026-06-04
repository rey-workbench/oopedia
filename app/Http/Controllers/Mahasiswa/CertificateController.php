<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Services\CertificateServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\CertificateResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Inertia\Response;
use Spatie\LaravelPdf\Facades\Pdf;

final class CertificateController extends Controller
{
    public function __construct(
        private readonly CertificateServiceInterface $certificateService,
    ) {}

    public function index(): Response
    {
        $certifications = $this->certificateService->getUserCertifications(Auth::id());

        return $this->render('Mahasiswa/Certificates/Index', [
            'certifications' => CertificateResource::collection($certifications)->resolve(),
        ]);
    }

    public function preview(string $materialId, ?string $userId = null): View
    {
        $userId ??= Auth::id();

        if (! $userId) {
            abort(403, 'User not identified');
        }

        $data = $this->certificateService->getCertificateData($materialId, $userId);

        return view('pdf.certificate', $data);
    }

    public function download(string $materialId): mixed
    {
        $userId = Auth::id();
        $data   = $this->certificateService->getCertificateData($materialId, $userId);

        return Pdf::view('pdf.certificate', $data)
            ->landscape()
            ->name(sprintf('Sertifikat_%s_%s.pdf', $data['tier_name'], $data['material_title']))
            ->download();
    }
}
