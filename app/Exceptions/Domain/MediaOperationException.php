<?php

namespace App\Exceptions\Domain;

use Symfony\Component\HttpFoundation\Response;

/**
 * Thrown when a media file operation fails (upload, delete, etc.).
 */
class MediaOperationException extends DomainException
{
    public function __construct(string $message = 'Operasi media gagal.')
    {
        parent::__construct($message, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
