<?php

namespace App\Services;

class BloodPressureService
{
    /**
     * Classify a blood pressure reading into a category and color.
     */
    public function classify(int $systolic, int $diastolic): array
    {
        // 1. Low Blood Pressure (Purple)
        if ($systolic <= 90 || $diastolic <= 60) {
            return [
                'label' => 'Low blood pressure',
                'color' => '#800080', // Purple
            ];
        }

        // 2. Ideal Blood Pressure (Green)
        if (
            $systolic >= 90 && $systolic <= 120 &&
            $diastolic >= 60 && $diastolic <= 80
        ) {
            return [
                'label' => 'Ideal blood pressure',
                'color' => '#008000', // Green
            ];
        }

        // 3. Pre-high Blood Pressure (Yellow-green)
        if (
            $systolic >= 120 && $systolic <= 140 &&
            $diastolic >= 80 && $diastolic <= 90
        ) {
            return [
                'label' => 'Pre-high blood pressure',
                'color' => '#c7ca0bff', // Yellow-green
            ];
        }

        // 4. High Blood Pressure (Red)
        if ($systolic >= 140 || $diastolic >= 90) {
            return [
                'label' => 'High blood pressure',
                'color' => '#FF0000', // Red
            ];
        }

        // Fallback (should almost never be used)
        return [
            'label' => 'Ideal blood pressure',
            'color' => '#008000',
        ];
    }

    /**
     * Check if a systolic/diastolic pair is realistic in real-world physiology.
     */
    public function isRealisticCombination(int $systolic, int $diastolic): bool
    {
        $difference = $systolic - $diastolic;

        // Difference should be at least 20 and not more than 100
        if ($difference < 20 || $difference > 100) {
            return false;
        }

        return true;
    }
}
