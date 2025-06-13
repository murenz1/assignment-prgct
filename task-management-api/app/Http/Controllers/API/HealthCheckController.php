<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HealthCheckController extends Controller
{
    /**
     * Check the health status of the API
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function check()
    {
        $dbStatus = 'OK';
        
        try {
            // Test database connection
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $dbStatus = 'ERROR: ' . $e->getMessage();
        }
        
        return response()->json([
            'status' => 'online',
            'version' => config('app.version', '1.0.0'),
            'timestamp' => now()->toIso8601String(),
            'database' => $dbStatus,
            'environment' => config('app.env')
        ]);
    }
}
