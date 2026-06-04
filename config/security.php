<?php

declare(strict_types=1);

return [
    'headers' => [
        'X-Frame-Options'           => 'SAMEORIGIN',
        'X-Content-Type-Options'    => 'nosniff',
        'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains; preload',
        'Referrer-Policy'           => 'strict-origin-when-cross-origin',
        'X-XSS-Protection'          => '1; mode=block',
    ],

    'csp' => [
        'default-src'     => "'self'",
        'script-src'      => "'self' 'unsafe-inline' 'unsafe-eval' https: http: blob:",
        'worker-src'      => "'self' blob:",
        'style-src'       => "'self' 'unsafe-inline' https: http:",
        'img-src'         => "'self' data: https: http:",
        'font-src'        => "'self' data: https: http:",
        'connect-src'     => "'self' ws: wss: https: http:",
        'frame-src'       => "'self' https: http:",
        'media-src'       => "'self' data: https: http:",
        'object-src'      => "'none'",
        'base-uri'        => "'self'",
        'form-action'     => "'self'",
        'frame-ancestors' => "'self'",
        // 'upgrade-insecure-requests' => '', // Commented out to allow Vite dev server
        // 'block-all-mixed-content' => '',
    ],

    'permissions' => [
        'geolocation'          => '()',
        'microphone'           => '()',
        'camera'               => '()',
        'payment'              => '()',
        'usb'                  => '()',
        'magnetometer'         => '()',
        'gyroscope'            => '()',
        'accelerometer'        => '()',
        'ambient-light-sensor' => '()',
        'autoplay'             => '()',
        'vr'                   => '()',
        'xr-spatial-tracking'  => '()',
    ],
];
