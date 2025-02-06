<?php

namespace App\Domain\User\Resources;

use App\Domain\User\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/**
 * @mixin User
 */
#[OA\Schema(
    title: "User Resource",
    description: "User data resource",
    type: "object"
)]
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[OA\Property(property: "id", type: "integer", example: 1)]
    #[OA\Property(property: "name", type: "string", example: "John Doe")]
    #[OA\Property(property: "email", type: "string", format: "email", example: "johndoe@example.com")]
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
        ];
    }
}
