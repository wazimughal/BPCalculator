<?php

namespace App\Http\Controllers;

use App\Services\BloodPressureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BloodPressureController extends Controller
{
    private BloodPressureService $bpService;

    public function __construct(BloodPressureService $bpService)
    {
        $this->bpService = $bpService;
    }

    public function showForm(Request $request)
    {
        // Get ALL history from session
        $allHistory = $request->session()->get('bp_history', []);

        // Simple manual pagination
        $perPage = 5; // rows per page
        $page = max(1, (int) $request->query('page', 1));
        $total = count($allHistory);

        // Show latest first
        $allHistory = array_reverse($allHistory);

        $offset = ($page - 1) * $perPage;
        $historyPage = array_slice($allHistory, $offset, $perPage);

        $hasNext = $offset + $perPage < $total;
        $hasPrevious = $page > 1;

        return view('bp.form', [
            'systolic'      => null,
            'diastolic'     => null,
            'category'      => null,
            'color'         => null,
            'advice'        => null,
            'historyItems'  => $historyPage,
            'page'          => $page,
            'hasNext'       => $hasNext,
            'hasPrevious'   => $hasPrevious,
        ]);

    }

    public function calculate(Request $request)
    {
        // Base validation rules + friendly messages
        $rules = [
            'systolic' => [
                'required',
                'integer',
                'between:70,190',
                'gt:diastolic', // systolic must be greater than diastolic
            ],
            'diastolic' => [
                'required',
                'integer',
                'between:40,100',
            ],
        ];

        $messages = [
            'systolic.required'   => 'Please enter the systolic (upper) value.',
            'diastolic.required'  => 'Please enter the diastolic (lower) value.',
            'systolic.between'    => 'Systolic should be between 70 and 190 mmHg for realistic readings.',
            'diastolic.between'   => 'Diastolic should be between 40 and 100 mmHg for realistic readings.',
            'systolic.gt'         => 'The systolic (upper) value must be higher than the diastolic (lower) value.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        // Extra realistic combination check using the service
        $validator->after(function ($validator) use ($request) {
            $systolic = $request->input('systolic');
            $diastolic = $request->input('diastolic');

            if (!is_numeric($systolic) || !is_numeric($diastolic)) {
                return;
            }

            $systolic = (int) $systolic;
            $diastolic = (int) $diastolic;

            if (!$this->bpService->isRealisticCombination($systolic, $diastolic)) {
                $validator->errors()->add(
                    'diastolic',
                    'These blood pressure values do not look realistic. ' .
                    'Normally, the difference between systolic and diastolic is at least 20 mmHg ' .
                    'and rarely more than about 100 mmHg. Please double-check your numbers.'
                );
            }
        });

        if ($validator->fails()) {
            // 🔴 Telemetry for invalid input
            Log::warning('BP invalid input', [
                'input'  => $request->only(['systolic', 'diastolic']),
                'errors' => $validator->errors()->all(),
                'timestamp' => now()->toDateTimeString(),
            ]);

            return redirect()
                ->route('bp.form')
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();

        $systolic = (int) $validated['systolic'];
        $diastolic = (int) $validated['diastolic'];

        // Use the SERVICE for classification (this is what we test later)
        $result = $this->bpService->classify($systolic, $diastolic);

        // Build telemetry entry for valid calculation
        $entry = [
            'timestamp' => now()->toDateTimeString(),
            'systolic'  => $systolic,
            'diastolic' => $diastolic,
            'category'  => $result['label'],
        ];

        // 1) Write to laravel.log (backend telemetry)
        Log::info('BP calculation performed', $entry);

        // 2) Save full history in session (for visible telemetry)
        $history = $request->session()->get('bp_history', []);
        $history[] = $entry;
        $request->session()->put('bp_history', $history);

        // Paginate history (always show page 1 after new calculation)
        $allHistory = array_reverse($history);
        $perPage = 5;
        $page = 1;
        $total = count($allHistory);
        $offset = 0;

        $historyPage = array_slice($allHistory, $offset, $perPage);
        $hasNext = $perPage < $total;
        $hasPrevious = false;

        return view('bp.form', [
            'systolic'      => $systolic,
            'diastolic'     => $diastolic,
            'category'      => $result['label'],
            'color'         => $result['color'],
            'advice'        => $result['advice'],
            'historyItems'  => $historyPage,
            'page'          => $page,
            'hasNext'       => $hasNext,
            'hasPrevious'   => $hasPrevious,
        ]);
    }
}
