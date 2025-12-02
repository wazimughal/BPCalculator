<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BloodPressureController extends Controller
{
    public function showForm()
    {
        // Just show the empty form
        return view('bp.form');
    }

    public function calculate(Request $request)
    {
        // 1) Validate input
        $validated = $request->validate([
            'systolic' => [
                'required',
                'integer',
                'between:70,190',
                'gt:diastolic',      // systolic must be greater than diastolic
            ],
            'diastolic' => [
                'required',
                'integer',
                'between:40,100',
            ],
        ]);

        $systolic = $validated['systolic'];
        $diastolic = $validated['diastolic'];

        // 2) Calculate category
        $category = $this->calculateCategory($systolic, $diastolic);

        // 3) Basic telemetry logging (we will extend this later)
        Log::info('BP calculation performed', [
            'systolic'  => $systolic,
            'diastolic' => $diastolic,
            'category'  => $category,
        ]);

        // 4) Return view with result
        return view('bp.form', compact('systolic', 'diastolic', 'category'));
    }

    private function calculateCategory(int $systolic, int $diastolic): string
    {
        // Ranges given in the assignment:
        // systolic: 70–190, diastolic: 40–100, systolic > diastolic
        // Lower limits are inclusive for each category.

        // Low blood pressure
        if ($systolic < 90 || $diastolic < 60) {
            return 'Low blood pressure';
        }

        // High blood pressure
        if ($systolic >= 140 || $diastolic >= 90) {
            return 'High blood pressure';
        }

        // Pre-high blood pressure
        if ($systolic >= 120 || $diastolic >= 80) {
            return 'Pre-high blood pressure';
        }

        // Otherwise ideal
        return 'Ideal blood pressure';
    }
}
