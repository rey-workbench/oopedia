<?php

declare(strict_types=1);

namespace App\Exceptions\Domain;

use Symfony\Component\HttpFoundation\Response;

final class QuestionNotFoundException extends DomainException
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf("Soal dengan ID '%s' tidak ditemukan.", $id), Response::HTTP_NOT_FOUND);
    }
}
