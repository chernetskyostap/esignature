<?php

namespace App\Domain\Document\Models;

use App\Domain\User\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $path
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Document extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'path',
    ];

    protected $with = ['signatureRequests'];

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

    public function signatureRequests(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'signature_requests')
            ->withPivot(['id', 'status', 'user_id']);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function setUser(User $user): self
    {
        $this->user()->associate($user);
        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }
}
