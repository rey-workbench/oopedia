<?php

namespace App\Exceptions\Domain;

use Symfony\Component\HttpFoundation\Response;

/**
 * Thrown when a quiz attempt operation fails (e.g., not found or invalid state).
 */
class QuizAttemptException extends DomainException
{
    public function __construct(string $message = 'Terjadi kesalahan pada sesi kuis.')
    {
        parent::__construct($message, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
