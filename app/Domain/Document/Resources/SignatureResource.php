<?php

namespace App\Domain\Document\Resources;

use App\Domain\Document\Models\Document;
use App\Domain\Document\Models\SignatureRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/**
 * @mixin SignatureRequest
 */
#[OA\Schema(
    title: "Signature Resource",
    description: "Signature data resource",
    type: "object"
)]
class SignatureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[OA\Property(property: "id", type: "integer", example: 1)]
    #[OA\Property(property: "user_id", type: "integer", example: 2)]
    #[OA\Property(property: "document_id", type: "integer", example: 27)]
    #[OA\Property(property: "status", type: "string", example: "pending")]
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->pivot->id ?? $this->id,
            'user_id' => $this->pivot->user_id ?? $this->user_id,
            'document_id' => $this->pivot->document_id ?? $this->document_id,
            'status' => $this->pivot->status ?? $this->status,
        ];
    }
}
