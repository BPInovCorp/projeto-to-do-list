<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Utilizador;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $utilizador = Utilizador::create([
            'nome' => $request->validated('nome'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
        ]);

        Auth::login($utilizador);
        $request->session()->regenerate();

        return response()->json([
            'status' => 'sucesso',
            'utilizador' => $utilizador,
            'csrf_token' => csrf_token(),
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $throttleKey = Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return response()->json([
                'message' => 'Demasiadas tentativas de início de sessão. Por favor, tente novamente em ' . $seconds . ' segundos.',
            ], 429);
        }

        $credentials = [
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
        ];

        if (!Auth::attempt($credentials)) {
            RateLimiter::hit($throttleKey, 60);

            return response()->json([
                'message' => 'As credenciais introduzidas não correspondem aos nossos registos.',
                'errors' => [
                    'email' => ['As credenciais introduzidas não correspondem aos nossos registos.'],
                ],
            ], 422);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        return response()->json([
            'status' => 'sucesso',
            'utilizador' => Auth::user(),
            'csrf_token' => csrf_token(),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'status' => 'sucesso',
            'csrf_token' => csrf_token(),
        ]);
    }

    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'utilizador' => $request->user(),
        ]);
    }
}