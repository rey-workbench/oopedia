<?php

namespace App\Exceptions\Domain;

use Symfony\Component\HttpFoundation\Response;

class QuizAttemptException extends DomainException
{
    public function __construct(string $message = 'Terjadi kesalahan pada sesi kuis.')
    {
        parent::__construct($message, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
