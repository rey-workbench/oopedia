<?php

declare(strict_types=1);

namespace App\Contracts\Services;

interface CertificateServiceInterface
{
    /**
     * @return array{
     *     student_name: string,
     *     material_title: string,
     *     material_id: string,
     *     tier_name: string,
     *     tier_color: string,
     *     tier_color_dark: string,
     *     logo_image: string,
     *     sign_image: string,
     *     qr_code: string,
     *     date: string,
     * }
     */
    public function getCertificateData(string $materialId, string $userId): array;

    public function getUserCertifications(string $userId): array;
}
