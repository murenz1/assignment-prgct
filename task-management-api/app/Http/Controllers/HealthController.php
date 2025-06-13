<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    /**
     * Check the health of the application
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function check()
    {
        $status = 'ok';
        $message = 'API is running';
        $dbStatus = 'ok';

        // Check database connection
        try {
            DB::connection()->getPdo();
            $dbStatus = 'ok';
        } catch (\Exception $e) {
            $status = 'warning';
            $dbStatus = 'error';
            $message = 'Database connection failed';
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'timestamp' => now()->toIso8601String(),
            'services' => [
                'database' => $dbStatus,
                'api' => 'ok'
            ]
        ]);
    }
}
