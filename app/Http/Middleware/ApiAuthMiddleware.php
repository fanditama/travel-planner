<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Parse token dari header Authorization dengan JWT
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json([
                    'errors' => [
                        'message' => [
                            'User not found'
                        ]
                    ]
                ], 404);
            }

            // Jika autentikasi berhasil
            Auth::guard('api')->login($user);
        } catch (TokenExpiredException $e) {
            //Token sudah kadaluarsa
            return response()->json([
                'errors' => [
                    'message' => [
                        'Token Expired'
                    ]
                ]
            ], 401);
        } catch (TokenInvalidException $e) {
            // Token tidak valid
             return response()->json([
                'errors' => [
                    'message' => [
                        'Token Invalid'
                    ]
                ]
            ], 401);
        } catch (JWTException $e) {
            // Token tidak ada atau ada error lain saat parsing/validasi token
             return response()->json([
                'errors' => [
                    'message' => [
                        'Token Absent or Invalid Format'
                    ]
                ]
            ], 401);
        }

        // Jika autentikasi JWT berhasil
        return $next($request);
    }
}
