<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\TarifAirDetail;

class TariffCalculationService
{
    /**
     * Calculate water tariff based on usage and tariff detail.
     *
     * @param  int  $usageM3  Water usage in cubic meters
     * @param  TarifAirDetail  $tarifDetail  Tariff detail with progressive rates
     * @param  float  $fixedCharge  Fixed charge / Abonemen (default: from rp_minimum)
     * @param  float  $adminFee  Administration fee
     * @param  float  $sanitationLevy  Sanitation levy / Biaya pemeliharaan
     * @return array{usage_breakdown: array<int, array{block: int, range: string, usage: int, rate: float, subtotal: float}>, water_fee_subtotal: float, fixed_charge: float, admin_fee: float, sanitation_levy: float, total: float}
     */
    public function calculate(
        int $usageM3,
        TarifAirDetail $tarifDetail,
        ?float $fixedCharge = null,
        ?float $adminFee = null,
        ?float $sanitationLevy = null
    ): array {
        // Use defaults from tariff detail if not provided
        // Note: fixedCharge is separate from rp_minimum (which is the minimum water charge)
        $fixedCharge = $fixedCharge ?? 0; // Default fixed charge / abonemen
        $adminFee = $adminFee ?? (float) ($tarifDetail->golongan->by_administrasi ?? 5000);
        // Get sanitation levy from by_pemeliharaan
        $sanitationLevy = $sanitationLevy ?? (float) ($tarifDetail->golongan->by_pemeliharaan ?? 3000);

        // Get minimum and harga minimum
        $minimum = (int) ($tarifDetail->minimum ?? 0);
        $hargaMin = (float) ($tarifDetail->rp_minimum ?? 0);

        // If usage is less than or equal to minimum, return minimum charge (following original logic)
        if ($minimum >= $usageM3) {
            $usageBreakdown = [
                [
                    'block' => 1,
                    'range' => "0-{$minimum}",
                    'min' => 0,
                    'max' => $minimum,
                    'usage' => 0,
                    'rate' => $hargaMin,
                    'subtotal' => $hargaMin,
                ],
            ];
            $waterFeeSubtotal = $hargaMin;

            $total = $waterFeeSubtotal + $fixedCharge + $adminFee + $sanitationLevy;

            return [
                'pemakaian' => $usageBreakdown,
                'by_administrasi' => $adminFee,
                'by_pemeliharaan' => $sanitationLevy,
                'pemakaian_subtotal' => $waterFeeSubtotal,
                'total' => $total,
            ];
        }

        // Get progressive rates from progresif array
        $progresif = $tarifDetail->progresif ?? [];
        if (! is_array($progresif)) {
            $progresif = [];
        }

        // Ensure progresif is sorted by min value
        usort($progresif, fn($a, $b) => ($a['min'] ?? 0) <=> ($b['min'] ?? 0));

        $usageBreakdown = [];
        $waterFeeSubtotal = 0.0;
        $blockNumber = 1;
        $sisa = $usageM3;

        foreach ($progresif as $tg) {
            $min = (int) ($tg['min'] ?? 0);
            $max = (int) ($tg['max'] ?? 0);
            $harga = (float) ($tg['harga'] ?? 0);

            // Calculate selisih (range size for this tier)
            $selisih = $max - ($min == 0 ? 0 : ($min - 1));

            if ($sisa <= 0) {
                break;
            }

            // Calculate usage and cost for this tier
            if ($sisa >= $selisih) {
                $tierUsage = $selisih;
                $tierSubtotal = $harga * $selisih;
            } else {
                $tierUsage = $sisa;
                $tierSubtotal = $harga * $sisa;
            }

            $waterFeeSubtotal += $tierSubtotal;

            // Format range display
            $rangeDisplay = $max > 0 ? "{$min}-{$max}" : ">{$min}";

            $usageBreakdown[] = [
                'block' => $blockNumber,
                'range' => $rangeDisplay,
                'min' => $min,
                'max' => $max,
                'usage' => $tierUsage,
                'rate' => $harga,
                'subtotal' => $tierSubtotal,
            ];

            $sisa = $sisa - $selisih;
            $blockNumber++;

            if ($sisa <= 0) {
                break;
            }
        }

        $total = $waterFeeSubtotal + $fixedCharge + $adminFee + $sanitationLevy;

        return [
            'pemakaian' => $usageBreakdown,
            'by_administrasi' => $adminFee,
            'by_pemeliharaan' => $sanitationLevy,
            'pemakaian_subtotal' => $waterFeeSubtotal,
            'total' => $total,
        ];
    }

    /**
     * Format number as Indonesian Rupiah currency.
     */
    public static function formatRupiah(float|int $amount): string
    {
        return 'Rp ' . number_format((float) $amount, 0, ',', '.');
    }
}
