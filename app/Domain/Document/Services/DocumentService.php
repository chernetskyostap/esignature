<?php

namespace App\Domain\Document\Services;

use App\Domain\Document\Models\Document;
use App\Domain\User\Models\User;
use Illuminate\Http\UploadedFile;

class DocumentService
{
    public function list(User $user)
    {
        return Document::forUser($user)->paginate();
    }
    public function upload(UploadedFile $file, User $user): Document
    {
        $path = $file->store('uploads/pdf', 'public');

        return Document::create([
            'user_id' => $user->id,
            'name' => $file->getFilename(),
            'path' => $path,
        ]);
    }
}
