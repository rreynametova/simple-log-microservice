<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLogRequest;
use App\Models\LogEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogController extends Controller
{
    public function store(StoreLogRequest $request) {
        $validatedData = $request->validated();

        try {
            $logEntry = LogEntry::create([
                'trace_id' => $validatedData['trace_id'],
                'timestamp' => $validatedData['timestamp'],
                'type' => $validatedData['type'],
                'log_data' => $validatedData['log_data'],
            ]);

            return response()->json([
                'message' => 'Log entry created successfully',
                'log_id' => $logEntry->id,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to store log entry: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data' => $validatedData,
            ]);

            return response()->json([
                'message' => 'Failed to store log entry',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
