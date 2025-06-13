<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    /**
     * Public test endpoint that doesn't require authentication
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function publicTest()
    {
        return response()->json([
            'message' => 'This is a public endpoint that anyone can access',
            'timestamp' => now()->toIso8601String()
        ]);
    }

    /**
     * Protected test endpoint that requires authentication
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function protectedTest(Request $request)
    {
        return response()->json([
            'message' => 'You have successfully accessed a protected endpoint',
            'user' => $request->user(),
            'timestamp' => now()->toIso8601String()
        ]);
    }
}
