<?php

namespace App\Exceptions\Domain;

use Symfony\Component\HttpFoundation\Response;

/**
 * Thrown when a requested sub-material cannot be found.
 */
class SubMaterialNotFoundException extends DomainException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Sub-materi dengan ID '{$id}' tidak ditemukan.", Response::HTTP_NOT_FOUND);
    }
}
