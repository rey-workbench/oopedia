<?php

namespace App\Exceptions\Domain;

use Symfony\Component\HttpFoundation\Response;

class InvalidAnswerException extends DomainException
{
    public function __construct(string $message = 'Jawaban yang dikirimkan tidak valid.')
    {
        parent::__construct($message, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
