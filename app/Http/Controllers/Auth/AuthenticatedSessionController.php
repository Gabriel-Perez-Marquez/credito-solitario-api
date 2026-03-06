<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 422);
        }

        $user = Auth::user();

        $origen = $request->input('origen', 'mobile');

        if ($origen === 'web' && $user->cliente) { 
            Auth::logout(); 
            return response()->json([
                'message' => 'Acceso denegado. Esta cuenta no tiene permisos de administrador.'
            ], 403); 
        }

        if ($origen === 'mobile' && !$user->cliente) { 
            Auth::logout(); 
            return response()->json([
                'message' => 'Acceso denegado. Los administradores deben usar el panel web.'
            ], 403); 
        }

        $token = $request->user()->createToken('api')->plainTextToken;
        
        return response()->json([
            'token' => $token,
            'user'  => $user,
        ], 200);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $request->user()->currentAccessToken()?->delete();
        return response()->noContent();
    }
}
