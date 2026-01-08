<?php

namespace App\Domain\Payments\Enums;

enum PaymentStatus: string
{
    case PENDING    = 'pending';
    case PROCESSING = 'processing';
    case PAID       = 'paid';
    case FAILED     = 'failed';
}
