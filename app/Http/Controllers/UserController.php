<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        // cek email sudah ada di dalam database
        if (User::where('email', $data['email'])->count() == 1) {
            throw new HttpResponseException(response([
                'errors' => [
                    'email' => [
                        'email already registered'
                    ]
                ]
            ], 400));
        }

        $user = new User($data);
        $user->password = Hash::make($data['password']);
        $user->save();

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    public function login(UserLoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        // cek email dan password sudah ada di dalam database
        $user = User::where('email', $data['email'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'email or password wrong'
                    ]
                ]
            ], 401));
        }

        // Generate JWT token
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'data' => new UserResource($user),
            'token' => $token
        ]);
    }

    public function get(Request $request): UserResource
    {
        $auth = Auth::guard('api')->user(); // cek user yg sudah login
        return new UserResource($auth);
    }

    public function update(UserUpdateRequest $request): UserResource
    {
        $data = $request->validated();

        $user = Auth::guard('api')->user(); // cek user yg sudah login


        if (isset($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        
        if (isset($data['name'])) {
            $user->name = $data['name'];
        }

        if (isset($data['preferred_activity'])) {
            $user->preferred_activity = $data['preferred_activity'];
        }

        if (isset($data['preferred_travel_style'])) {
            $user->preferred_travel_style = $data['preferred_travel_style'];
        }

        if (isset($data['home_location'])) {
            $user->home_location = $data['home_location'];
        }

        /** @var \App\Models\User $user **/
        $user->save();

        return new UserResource($user);
    }

    public function logout(): JsonResponse
    {
        Auth::guard('api')->logout();

        return response()->json([
            "data" => true
        ])->setStatusCode(200);
    }
}
