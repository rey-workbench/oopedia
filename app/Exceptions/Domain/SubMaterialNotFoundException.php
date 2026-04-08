<?php

namespace App\Exceptions\Domain;

use Symfony\Component\HttpFoundation\Response;

class SubMaterialNotFoundException extends DomainException
{
    public function __construct(string $id)
    {
        parent::__construct("Sub-materi dengan ID '{$id}' tidak ditemukan.", Response::HTTP_NOT_FOUND);
    }
}
