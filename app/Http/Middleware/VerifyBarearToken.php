<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Users; 

class VerifyBarearToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json(['error' => 'Token não fornecido'], 401);
        }

        // Busca o usuário com base no token
        $user = Users::where('remember_token', $token)->first();

        if (!$user) {
            return response()->json(['error' => 'Token inválido'], 401);
        }

        // Se o usuário foi encontrado, anexa o usuário à solicitação para uso posterior
        $request->user = $user;

        return $next($request);
    }
}
