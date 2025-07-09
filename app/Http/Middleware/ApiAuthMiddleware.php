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
            // Parse token dan ambil payload
            $payload = JWTAuth::parseToken()->getPayload();
            $userId = $payload->get('sub');
            
            // Cari user berdasarkan ID dari token
            $user = \App\Models\User::find($userId);
            
            if (!$user) {
                return response()->json([
                    'errors' => [
                        'message' => [
                            'User not found'
                        ]
                    ]
                ], 404);
            }

            // Set user untuk request ini
            Auth::guard('api')->login($user);
            
        } catch (TokenExpiredException $e) {
            return response()->json([
                'errors' => [
                    'message' => [
                        'Token Expired'
                    ]
                ]
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'errors' => [
                    'message' => [
                        'Token Invalid'
                    ]
                ]
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ], 401);
        }

        return $next($request);
    }
}
