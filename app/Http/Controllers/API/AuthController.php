<?php

namespace App\Http\Controllers\API;

use App\Domain\Common\Requests\LoginRequest;
use App\Domain\Common\Requests\RegisterRequest;
use App\Domain\Common\Services\AuthService;
use App\Domain\User\Resources\UserResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Authentication", description: "API Endpoints for User Authentication")]
class AuthController extends Controller
{
    #[OA\Post(
        path: "/api/register",
        summary: "User Registration",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "email", "password"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "John Doe"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "johndoe@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password123")
                ]
            )
        ),
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 201,
                description: "User created successfully",
                content: new OA\JsonContent(ref: "#/components/schemas/UserResource")
            ),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function register(RegisterRequest $request, AuthService $authService): JsonResource
    {
        $user = $authService->register($request->toDto());

        return UserResource::make($user);
    }

    #[OA\Post(
        path: "/api/login",
        summary: "User Login",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "johndoe@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password123")
                ]
            )
        ),
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful login",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "token", type: "string", example: "AHu28ahwhasf737hsadhSs")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthorized")
        ]
    )]
    public function login(LoginRequest $request)
    {
        if (!Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = Auth::user()
            ->createToken('auth_token')
            ->plainTextToken;

        return response()->json(['token' => $token], 200);
    }

    #[OA\Post(
        path: "/api/logout",
        summary: "User Logout",
        security: [["bearerAuth" => []]],
        tags: ["Authentication"],
        responses: [
            new OA\Response(response: 204, description: "Successfully logged out"),
            new OA\Response(response: 401, description: "Unauthorized")
        ]
    )]
    public function logout(): JsonResponse
    {
        Auth::user()->currentAccessToken()->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
