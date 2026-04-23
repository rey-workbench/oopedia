<?php

namespace App\Rules\Adaptive;

use App\Models\AdaptiveFact;
use Illuminate\Support\Facades\Cache;

/**
 * Registry dinamis untuk memetakan nama fakta (Constant) ke kode (Database).
 * Menggunakan caching untuk performa tinggi.
 */
final class FactRegistry
{
    private static ?array $map = null;

    /**
     * Ambil kode fakta (Gxx) berdasarkan nama konstanta.
     */
    public static function getCode(string $name): ?string
    {
        if (self::$map === null) {
            self::$map = Cache::remember('adaptive_fact_codes_map', 3600, function () {
                return AdaptiveFact::pluck('code', 'name')->toArray();
            });
        }

        return self::$map[$name] ?? null;
    }

    /**
     * Ambil daftar kode fakta (Gxx) dari array nama konstanta.
     */
    public static function getCodes(array $names): array
    {
        $codes = [];
        foreach ($names as $name) {
            $code = self::getCode($name);
            if ($code) {
                $codes[] = $code;
            }
        }

        return $codes;
    }

    /**
     * Ambil kode fakta (Gxx) berdasarkan raw code string (misalnya 'G01').
     * Digunakan oleh seeder ketika kode langsung tersedia.
     * Jika kode sudah valid (ada di DB), dikembalikan langsung.
     */
    public static function getCodeByRaw(string $rawCode): ?string
    {
        // V-codes (virtual/deduced facts) dikembalikan as-is
        if (str_starts_with($rawCode, 'V')) {
            return $rawCode;
        }

        // G-codes: verifikasi ada di map, lalu kembalikan
        if (self::$map === null) {
            self::$map = Cache::remember('adaptive_fact_codes_map', 3600, function () {
                return AdaptiveFact::pluck('code', 'name')->toArray();
            });
        }

        // map is name → code, so we need to find by value
        return in_array($rawCode, self::$map, true) ? $rawCode : null;
    }

    /**
     * Clear cache jika ada perubahan data di database.
     */
    public static function clear(): void
    {
        self::$map = null;
        Cache::forget('adaptive_fact_codes_map');
    }
}
