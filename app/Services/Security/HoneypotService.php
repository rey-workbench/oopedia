<?php

declare(strict_types=1);

namespace App\Services\Security;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

final class HoneypotService
{
    /**
     * Common keywords used by directory scanners and fuzzers.
     *
     * @var array<int, string>
     */
    private const array KEYWORDS = [
        'wp-',
        '.env',
        'passwd',
        '.git',
        '.bak',
        '.swp',
        'config',
        'admin',
    ];

    /**
     * Check if the request path matches honeypot keywords and return the trap response if so.
     */
    public static function intercept(Request $request): ?Response
    {
        $path = strtolower($request->path());

        foreach (self::KEYWORDS as $keyword) {
            if (str_contains($path, $keyword)) {
                Log::warning('Honeypot Triggered', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'path' => $request->path(),
                ]);

                return self::trapResponse($request->ip());
            }
        }

        return null;
    }

    /**
     * Generate the ASCII art trap response.
     */
    private static function trapResponse(?string $ip): Response
    {
        $location = 'Unknown Location';

        if ($ip && $ip !== '127.0.0.1' && $ip !== '::1') {
            try {
                // Fetch location from free GeoIP API (ip-api.com)
                $geo = Http::timeout(3)->get('http://ip-api.com/json/' . $ip)->json();
                if (isset($geo['status']) && $geo['status'] === 'success') {
                    $location = sprintf('%s, %s, %s', $geo['city'], $geo['regionName'], $geo['country']);
                }
            } catch (\Throwable) {
                // Ignore API failures silently to not break the honeypot
            }
        } else {
            $location = 'Localhost (Your own computer)';
        }

        $asciiArt = <<<ASCII
  ___  __      __ ___  _      _       _  __ ___  _      _     __   __ ___  _   _ 
 |_ _| \ \    / /|_ _|| |    | |     | |/ /|_ _|| |    | |    \ \ / // _ \| | | |
  | |   \ \/\/ /  | | | |    | |     | ' /  | | | |    | |     \ V /| | | | | | |
  | |    \_/\_/   | | | |___ | |___  | . \  | | | |___ | |___   | | | |_| | |_| |
 |___|            |___||_____||_____| |_|\_\|___||_____||_____|  |_|  \___/ \___/ 

YOUR IP ADDRESS ({$ip}) HAS BEEN LOGGED.
YOUR LOCATION: {$location}
ASCII;

        return response($asciiArt, 404)->header('Content-Type', 'text/plain');
    }
}
