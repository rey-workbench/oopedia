<?php

declare(strict_types=1);

namespace App\Exceptions\Domain;

use Symfony\Component\HttpFoundation\Response;

final class UserNotFoundException extends DomainException
{
    public function __construct(string $id)
    {
        parent::__construct("Pengguna dengan ID '{$id}' tidak ditemukan.", Response::HTTP_NOT_FOUND);
    }
}
