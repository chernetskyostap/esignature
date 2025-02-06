<?php

namespace App\Domain\Document\Enums;

enum DocumentStatusEnum: string
{
    case PENDING = 'pending';
    case SIGNED = 'signed';
}
