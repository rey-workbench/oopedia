<?php

namespace App\Exceptions\Domain;

use RuntimeException;

/**
 * Base domain exception for all application-level exceptions.
 *
 * All custom domain exceptions should extend this class to allow
 * centralized handling in the exception Handler.
 */
abstract class DomainException extends RuntimeException
{
}
