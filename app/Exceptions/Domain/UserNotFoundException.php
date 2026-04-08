<?php

namespace App\Exceptions\Domain;

use Symfony\Component\HttpFoundation\Response;

class UserNotFoundException extends DomainException
{
    public function __construct(string $id)
    {
        parent::__construct("Pengguna dengan ID '{$id}' tidak ditemukan.", Response::HTTP_NOT_FOUND);
    }
}
