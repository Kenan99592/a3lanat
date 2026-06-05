<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'phone'        => 'nullable|string|max:20',
            'password'     => 'required|string|min:8|confirmed',
        ]);

        $result = $this->authService->register($validated);

        return response()->json([
            'message' => 'تم إنشاء الحساب بنجاح. لديك 14 يوم تجريبي مجاني.',
            'user'    => $result['user'],
            'token'   => $result['token'],
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        try {
            $result = $this->authService->login($validated);

            return response()->json([
                'message' => 'تم تسجيل الدخول بنجاح.',
                'user'    => $result['user'],
                'token'   => $result['token'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'بيانات الدخول غير صحيحة.',
                'errors'  => $e->errors(),
            ], 422);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([
            'message' => 'تم تسجيل الخروج بنجاح.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user()->load('tenant', 'subscriptionPlan'),
        ]);
    }
}
