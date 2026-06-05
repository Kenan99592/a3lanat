<?php

namespace App\Services\Auth;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(array $data): array
    {
        $tenant = Tenant::create([
            'name' => $data['company_name'] ?? $data['name'],
            'slug' => Str::slug($data['company_name'] ?? $data['name']) . '-' . Str::random(6),
        ]);

        $user = User::create([
            'tenant_id'     => $tenant->id,
            'name'          => $data['name'],
            'email'         => $data['email'],
            'phone'         => $data['phone'] ?? null,
            'password'      => Hash::make($data['password']),
            'role'          => 'client',
            'status'        => 'active',
            'trial_ends_at' => now()->addDays(14),
        ]);

        $tenant->tenantUsers()->create([
            'user_id'   => $user->id,
            'role'      => 'owner',
            'is_active' => true,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user'  => $user->load('tenant', 'subscriptionPlan'),
            'token' => $token,
        ];
    }

    public function login(array $data): array
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['البريد الإلكتروني أو كلمة المرور غير صحيحة.'],
            ]);
        }

        if ($user->status === 'suspended') {
            throw ValidationException::withMessages([
                'email' => ['تم تعليق حسابك. تواصل مع الدعم.'],
            ]);
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user'  => $user->load('tenant', 'subscriptionPlan'),
            'token' => $token,
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
