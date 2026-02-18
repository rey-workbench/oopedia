<?php

namespace App\Exceptions\Domain;

use Symfony\Component\HttpFoundation\Response;

/**
 * Thrown when a requested question cannot be found.
 */
class QuestionNotFoundException extends DomainException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Soal dengan ID '{$id}' tidak ditemukan.", Response::HTTP_NOT_FOUND);
    }
}
