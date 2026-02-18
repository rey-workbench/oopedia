<?php

namespace App\Exceptions\Domain;

use Symfony\Component\HttpFoundation\Response;

/**
 * Thrown when the adaptive engine encounters an error during evaluation.
 */
class AdaptiveEngineException extends DomainException
{
    public function __construct(string $message = 'Adaptive engine gagal mengevaluasi kondisi.')
    {
        parent::__construct($message, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
