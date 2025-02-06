<?php

namespace App\Domain\Document\Services;

use App\Domain\Document\Enums\DocumentStatusEnum;
use App\Domain\Document\Exceptions\DocumentIsPendingException;
use App\Domain\Document\Models\Document;
use App\Domain\Document\Models\SignatureRequest;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

class SignatureService
{
    /**
     * @throws DocumentIsPendingException
     */
    public function sendRequest(Document $document, int $userId): SignatureRequest
    {
        if (SignatureRequest::withoutGlobalScope('user_scope')
            ->where([
                'user_id' => $userId,
                'document_id' => $document->id,
                'status' => DocumentStatusEnum::PENDING->value
            ])->exists()) {
            throw new DocumentIsPendingException();
        }
        return SignatureRequest::create([
            'user_id' => $userId,
            'document_id' => $document->id,
            'status' => DocumentStatusEnum::PENDING,
        ]);
    }

    public function sign(SignatureRequest $signatureRequest, $text): void
    {
        $signatureRequest->setStatus(DocumentStatusEnum::SIGNED)
            ->save();

        $pdf = new Fpdi();
        $stream = Storage::disk('public')->readStream($signatureRequest->document->path);
        $pageCount = $pdf->setSourceFile($stream);

        for ($page = 1; $page <= $pageCount; $page++) {
            $pdf->AddPage();
            $tplIdx = $pdf->importPage($page);
            $pdf->useTemplate($tplIdx);
        }

        $pdf->SetFont('Helvetica');
        $pdf->SetTextColor(255, 0, 0);
        $pdf->SetXY(-50, 275);
        $pdf->Write(0, $text);

        $pdf->Output('D', 'signed.pdf');
    }
}
