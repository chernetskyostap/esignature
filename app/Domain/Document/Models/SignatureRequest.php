<?php

namespace App\Domain\Document\Models;

use App\Domain\Document\Enums\DocumentStatusEnum;
use App\Domain\User\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * @property int $id
 * @property int $user_id
 * @property int $document_id
 * @property DocumentStatusEnum $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class SignatureRequest extends Model
{
    protected $fillable = [
        'user_id',
        'document_id',
        'status',
    ];

    protected $casts = [
        'status' => DocumentStatusEnum::class
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('user_scope', static function (Builder $builder): void {
            $builder->where('user_id', Auth::user()->id);
        });
    }

    public function scopeForUser(Builder $query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    public function document(): HasOne
    {
        return $this->hasOne(Document::class, 'id')
            ->withoutGlobalScope('user_scope');
    }

    public function setStatus(DocumentStatusEnum $statusEnum): self
    {
        $this->status = $statusEnum;
        return $this;
    }
}
