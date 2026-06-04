<?php

declare(strict_types=1);

namespace App\Services\Lms;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\CertificateServiceInterface;
use App\Models\Material;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;

final readonly class CertificateService implements CertificateServiceInterface
{
    private const array TIER_PALETTES = [
        'gold'   => ['primary' => '#EAB308', 'dark' => '#854D0E'],
        'silver' => ['primary' => '#94A3B8', 'dark' => '#1E293B'],
        'bronze' => ['primary' => '#D97706', 'dark' => '#78350F'],
    ];

    public function __construct(
        private MaterialRepositoryInterface $materialRepository,
        private ProgressRepositoryInterface $progressRepository,
        private UserRepositoryInterface $userRepository,
    ) {}

    public function getCertificateData(string $materialId, string $userId): array
    {
        $user     = $this->userRepository->find($userId);
        $state    = $this->progressRepository->getOrCreateStudentState($userId);
        $material = $this->materialRepository->find($materialId);

        if (! $material instanceof Material) {
            abort(404, 'Materi tidak ditemukan');
        }

        $rawCertifications = $state->certifications          ?? [];
        $tier              = $rawCertifications[$materialId] ?? 'gold';
        $palette           = self::TIER_PALETTES[$tier]      ?? self::TIER_PALETTES['gold'];

        $qrUrl = route('mahasiswa.certificates.preview', [$materialId, $userId]);

        $builder = new Builder(
            data: $qrUrl,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            logoPath: public_path('images/logo.png'),
            logoResizeToWidth: 80,
            logoPunchoutBackground: true,
        );

        $qrCodeDataUri = $builder->build()->getDataUri();

        return [
            'student_name'    => $user?->name ?? 'Student',
            'material_title'  => $material->title,
            'material_id'     => $material->id,
            'tier_name'       => ucfirst($tier),
            'tier_color'      => $palette['primary'],
            'tier_color_dark' => $palette['dark'],
            'logo_image'      => $this->encodeImage('images/logo.png'),
            'sign_image'      => $this->encodeImage('images/certificates/sign.png'),
            'qr_code'         => $qrCodeDataUri,
            'date'            => now()->translatedFormat('d F Y'),
        ];
    }

    public function getUserCertifications(string $userId): array
    {
        $state             = $this->progressRepository->getOrCreateStudentState($userId);
        $rawCertifications = $state->certifications ?? [];

        return collect($rawCertifications)
            ->map(function (string $type, string $materialId): array {
                $material = $this->materialRepository->find($materialId);

                return [
                    'material_id'    => $materialId,
                    'material_title' => $material?->title ?? 'Unknown Material',
                    'type'           => $type,
                    'issued_at'      => null,
                ];
            })
            ->values()
            ->all();
    }

    private function encodeImage(string $relativePath): string
    {
        return 'data:image/png;base64,' . base64_encode(file_get_contents(public_path($relativePath)));
    }
}
