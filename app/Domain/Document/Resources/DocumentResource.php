<?php

namespace App\Domain\Document\Resources;

use App\Domain\Document\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;

/**
 * @mixin Document
 */
#[OA\Schema(
    title: "Document Resource",
    description: "Document data resource",
    type: "object"
)]
class DocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[OA\Property(property: "id", type: "integer", example: 1)]
    #[OA\Property(property: "user_id", type: "integer", example: 2)]
    #[OA\Property(property: "name", type: "string", example: "rent_contract")]
    #[OA\Property(property: "path", type: "string", example: "/tmp/pdf/test.pdf")]
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'path' => Storage::url($this->path),
            'signatures' => SignatureResource::collection($this->whenLoaded('signatureRequests'))
        ];
    }
}
