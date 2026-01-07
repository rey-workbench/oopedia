<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

abstract class BaseService
{
    /**
     * Log an informational message
     */
    protected function logInfo(string $message, array $context = [])
    {
        Log::info($message, $context);
    }

    /**
     * Log an error message
     */
    protected function logError(string $message, array $context = [])
    {
        Log::error($message, $context);
    }

    /**
     * Handle exceptions and log them
     */
    protected function handleException(\Exception $e, string $context = '')
    {
        $this->logError($context . ': ' . $e->getMessage(), [
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}
