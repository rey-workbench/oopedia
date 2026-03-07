<?php

namespace App\Exceptions\Domain;

use Symfony\Component\HttpFoundation\Response;

/**
 * Thrown when a requested material cannot be found.
 */
class MaterialNotFoundException extends DomainException
{
    public function __construct(string $id)
    {
        parent::__construct("Material dengan ID '{$id}' tidak ditemukan.", Response::HTTP_NOT_FOUND);
    }
}
