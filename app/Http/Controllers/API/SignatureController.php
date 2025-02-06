<?php

namespace App\Http\Controllers\API;

use App\Domain\Document\Enums\DocumentStatusEnum;
use App\Domain\Document\Models\SignatureRequest;
use App\Domain\Document\Resources\SignatureResource;
use App\Domain\Document\Services\SignatureService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;
use Throwable;

#[OA\Tag(name: "Signature", description: "API Endpoints for Signatures")]
class SignatureController extends Controller
{
    #[OA\Post(
        path: "/api/signatures/{signatureRequestId}/sign",
        summary: "Sign document",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["sign_text"],
                properties: [
                    new OA\Property(property: "sign_text", type: "string", example: 'My sign!'),
                ]
            )
        ),
        tags: ["Signatures"],
        parameters: [
            new OA\Parameter(
                name: 'signatureRequestId',
                in: 'path',
                required: true,
                schema: new OA\Schema(
                    type: 'integer',
                    default: 1,
                    minimum: 1,
                    example: 1
                )
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Document successfully signed",
            ),
            new OA\Response(response: 401, description: "Unauthorized"),
            new OA\Response(response: 500, description: "Something went wrong.")
        ]
    )]
    public function sign(
        SignatureRequest $signatureRequest,
        Request $request,
        SignatureService $signatureService
    ): Response {
        try {
            $signatureRequest->setStatus(DocumentStatusEnum::SIGNED)
                ->save();

            $signatureService->sign(
                $signatureRequest,
                $request->get('sign_text') ?: $this->currentUser()->name
            );
        } catch (Throwable) {
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }

    #[OA\Get(
        path: "/api/signatures/requests",
        summary: "Get signature requests list",
        security: [["bearerAuth" => []]],
        tags: ["Signatures"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Signature requests list",
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(
                            properties: [
                                new OA\Property(
                                    property: 'data',
                                    type: 'array',
                                    items: new OA\Items(ref: '#/components/schemas/SignatureResource')
                                ),
                                new OA\Property(
                                    property: 'meta',
                                    ref: '#/components/schemas/Pagination',
                                    type: 'object',
                                )
                            ],
                            type: 'object',
                        )
                    ]
                ),
            ),
            new OA\Response(response: 401, description: "Unauthorized"),
        ]
    )]
    public function getRequests(): JsonResource
    {
        return SignatureResource::collection(SignatureRequest::paginate());
    }
}
