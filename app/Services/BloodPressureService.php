<?php

namespace App\Services;

class BloodPressureService
{
    /**
     * Classify a blood pressure reading into a category, color, and advice text.
     */
    public function classify(int $systolic, int $diastolic): array
    {
        // 1. Low Blood Pressure (Purple)
        if ($systolic <= 90 || $diastolic <= 60) {
            return [
                'label'  => 'Low blood pressure',
                'color'  => '#800080', // Purple
                'advice' => 'Your blood pressure is lower than normal. If you feel unwell, dizzy, or lightheaded, consider contacting a healthcare professional.',
            ];
        }

        // 2. Ideal Blood Pressure (Green)
        if (
            $systolic >= 90 && $systolic <= 120 &&
            $diastolic >= 60 && $diastolic <= 80
        ) {
            return [
                'label'  => 'Ideal blood pressure',
                'color'  => '#008000', // Green
                'advice' => 'Your blood pressure is in the ideal range. Keep up your current healthy lifestyle, diet, and activity levels.',
            ];
        }

        // 3. Pre-high Blood Pressure (your custom color)
        if (
            $systolic >= 120 && $systolic <= 140 &&
            $diastolic >= 80 && $diastolic <= 90
        ) {
            return [
                'label'  => 'Pre-high blood pressure',
                'color'  => '#c7ca0bff', // Pre-high BP color
                'advice' => 'Your blood pressure is slightly elevated. Consider lifestyle changes, such as reducing salt, managing stress, and monitoring your readings.',
            ];
        }

        // 4. High Blood Pressure (Red)
        if ($systolic >= 140 || $diastolic >= 90) {
            return [
                'label'  => 'High blood pressure',
                'color'  => '#FF0000', // Red
                'advice' => 'Your blood pressure is high. Please consider speaking to a healthcare professional for advice and further checks.',
            ];
        }

        // Fallback (should almost never be used)
        return [
            'label'  => 'Ideal blood pressure',
            'color'  => '#008000',
            'advice' => 'Your blood pressure is close to the ideal range. Maintain a healthy lifestyle and keep monitoring it regularly.',
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
