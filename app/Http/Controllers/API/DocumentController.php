<?php

namespace App\Http\Controllers\API;

use App\Domain\Document\Exceptions\DocumentIsPendingException;
use App\Domain\Document\Models\Document;
use App\Domain\Document\Requests\SignatureSendRequest;
use App\Domain\Document\Requests\UploadDocumentRequest;
use App\Domain\Document\Resources\DocumentResource;
use App\Domain\Document\Resources\SignatureResource;
use App\Domain\Document\Services\DocumentService;
use App\Domain\Document\Services\SignatureService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;
use Throwable;

#[OA\Tag(name: "Documents", description: "API Endpoints for Documents")]
class DocumentController extends Controller
{
    #[OA\Get(
        path: "/api/documents",
        summary: "Get documents list",
        security: [["bearerAuth" => []]],
        tags: ["Documents"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Documents list",
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(
                            properties: [
                                new OA\Property(
                                    property: 'data',
                                    type: 'array',
                                    items: new OA\Items(ref: '#/components/schemas/DocumentResource')
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
    public function index(DocumentService $documentService): JsonResource
    {
        $documents = $documentService->list($this->currentUser());

        return DocumentResource::collection($documents);
    }

    #[OA\Get(
        path: "/api/documents/{documentId}",
        summary: "Get document",
        security: [["bearerAuth" => []]],
        tags: ["Documents"],
        parameters: [
            new OA\Parameter(
                name: 'documentId',
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
                description: "Document info",
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(
                            properties: [
                                new OA\Property(
                                    property: 'data',
                                    type: 'object',
                                    allOf: [
                                        new OA\Schema(ref: '#/components/schemas/DocumentResource')
                                    ]
                                ),
                            ],
                            type: 'object',
                        )
                    ]
                ),
            ),
            new OA\Response(response: 401, description: "Unauthorized"),
        ]
    )]
    public function show(Document $document): JsonResource
    {
        return DocumentResource::make($document);
    }

    #[OA\Post(
        path: "/api/documents/upload",
        summary: "Upload a PDF file",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ["file"],
                    properties: [
                        new OA\Property(property: "file", type: "string", format: "binary")
                    ]
                )
            )
        ),
        tags: ["Documents"],
        responses: [
            new OA\Response(
                response: 201,
                description: "Document uploaded successfully",
                content: new OA\JsonContent(ref: "#/components/schemas/DocumentResource")
            ),
            new OA\Response(response: 401, description: "Unauthorized"),
            new OA\Response(response: 400, description: "Invalid file format"),
            new OA\Response(response: 500, description: "Server error")
        ]
    )]
    public function upload(
        UploadDocumentRequest $request,
        DocumentService $documentService
    ): JsonResponse|JsonResource {
        try {
            $document = $documentService->upload(
                $request->file('file'),
                $this->currentUser()
            );

            return DocumentResource::make($document);
        } catch (Throwable) {
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }

    #[OA\Post(
        path: "/api/documents/{documentId}/send",
        summary: "Send document for signature",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["user_id"],
                properties: [
                    new OA\Property(property: "user_id", type: "integer", example: 12),
                ]
            )
        ),
        tags: ["Documents"],
        parameters: [
            new OA\Parameter(
                name: 'documentId',
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
                description: "Document successfully sent for signature",
                content: new OA\JsonContent(ref: "#/components/schemas/SignatureResource")
            ),
            new OA\Response(response: 401, description: "Unauthorized"),
            new OA\Response(response: 422, description: "Unprocessable Entity")
        ]
    )]
    public function send(
        Document $document,
        SignatureSendRequest $signatureSendRequest,
        SignatureService $signatureService,
    ) {
        try {
            $signatureRequest = $signatureService->sendRequest(
                $document,
                $signatureSendRequest->get('user_id')
            );

            return SignatureResource::make($signatureRequest);
        } catch (DocumentIsPendingException) {
            return response()->json(['error' => 'Your document is not sent. You have pending request'], 422);
        }
    }
}
