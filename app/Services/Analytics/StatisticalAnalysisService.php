<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use MathPHP\Statistics\Significance;

/**
 * StatisticalAnalysisService.
 * Menangani perhitungan statistik untuk kebutuhan skripsi (MSLQ & SUS).
 * Menggunakan MathPHP untuk uji signifikansi standar.
 */
final class StatisticalAnalysisService
{
    /**
     * Uji T (Independent T-Test).
     * Membandingkan rata-rata dua kelompok independen.
     */
    public function independentTTest(array $group1, array $group2): array
    {
        if (count($group1) < 2 || count($group2) < 2) {
            return ['error' => 'Sampel terlalu kecil'];
        }

        $result = Significance::tTest($group1, $group2);
        
        // Standarisasi key untuk frontend
        if (isset($result['p2'])) {
            $result['p-value'] = $result['p2'];
        }

        return $result;
    }

    /**
     * Hitung Reliabilitas Cronbach's Alpha.
     * r_11 = [k/(k-1)] * [1 - (Σσ_b^2 / σ_t^2)]
     */
    public function cronbachAlpha(array $matrix): float
    {
        $k = count($matrix[0] ?? []); // Jumlah butir
        if ($k <= 1) {
            return 0.0;
        }

        $itemVariances = [];
        $totalScores   = [];

        // Hitung varians tiap butir
        for ($i = 0; $i < $k; $i++) {
            $column            = array_column($matrix, $i);
            $itemVariances[]   = $this->variance($column);
        }

        // Hitung skor total tiap responden
        foreach ($matrix as $row) {
            $totalScores[] = array_sum($row);
        }

        $sumItemVariances = array_sum($itemVariances);
        $totalVariance    = $this->variance($totalScores);

        if ($totalVariance == 0) {
            return 0.0;
        }

        return ($k / ($k - 1)) * (1 - ($sumItemVariances / $totalVariance));
    }

    /**
     * Hitung Mann-Whitney U Test.
     */
    public function mannWhitneyU(array $group1, array $group2): array
    {
        $n1 = count($group1);
        $n2 = count($group2);

        // Gabungkan dan beri peringkat
        $combined = [];
        foreach ($group1 as $val) {
            $combined[] = ['group' => 1, 'value' => $val];
        }

        foreach ($group2 as $val) {
            $combined[] = ['group' => 2, 'value' => $val];
        }

        usort($combined, fn (array $a, array $b): int => $a['value'] <=> $b['value']);

        // Beri ranking (menangani ties)
        $ranks = $this->assignRanks($combined);

        $r1 = 0;
        $r2 = 0;
        foreach ($ranks as $rank) {
            if ($rank['group'] === 1) {
                $r1 += $rank['rank'];
            } else {
                $r2 += $rank['rank'];
            }
        }

        // Rumus U1 dan U2
        $u1 = ($n1 * $n2) + (($n1 * ($n1 + 1)) / 2) - $r1;
        $u2 = ($n1 * $n2) + (($n2 * ($n2 + 1)) / 2) - $r2;

        $uMin = min($u1, $u2);
        
        // Z-Score untuk n > 20
        $denom = sqrt(($n1 * $n2 * ($n1 + $n2 + 1)) / 12);
        $z = $denom == 0 ? 0 : ($uMin - ($n1 * $n2 / 2)) / $denom;

        // P-Value dari Z-Score (Dua sisi)
        $standardNormal = new \MathPHP\Probability\Distribution\Continuous\StandardNormal();
        $pValue = 2 * (1 - $standardNormal->cdf(abs($z)));

        return [
            'u_1'      => $u1,
            'u_2'      => $u2,
            'u_min'    => $uMin,
            'z_score'  => $z,
            'p-value'  => $pValue,
            'r_1'      => $r1,
            'r_2'      => $r2,
        ];
    }

    /**
     * Hitung Varians Populasi/Sampel.
     */
    public function variance(array $data, bool $isSample = true): float
    {
        $n = count($data);
        if ($n <= 1) {
            return 0.0;
        }

        $mean  = array_sum($data) / $n;
        $sumSq = 0;
        foreach ($data as $val) {
            $sumSq += ($val - $mean) ** 2;
        }

        return $sumSq / ($isSample ? $n - 1 : $n);
    }

    /**
     * Helper untuk assign ranking (dengan penanganan nilai yang sama/ties).
     */
    private function assignRanks(array $sortedData): array
    {
        $n = count($sortedData);
        for ($i = 0; $i < $n; $i++) {
            $j = $i;
            while ($j < $n - 1 && $sortedData[$j + 1]['value'] == $sortedData[$i]['value']) {
                $j++;
            }

            // Rata-rata ranking untuk nilai yang sama
            $rank = ($i + 1 + $j + 1) / 2;
            for ($k = $i; $k <= $j; $k++) {
                $sortedData[$k]['rank'] = $rank;
            }

            $i = $j;
        }

        return $sortedData;
    }
}
