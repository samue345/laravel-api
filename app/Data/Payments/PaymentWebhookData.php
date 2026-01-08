<?php

namespace App\Data\Payments;

use Spatie\LaravelData\Data;

class PaymentWebhookData extends Data
{
    public function __construct(
        public string $provider_payment_id,
        public string $status,    
        public string $event_id, 
    ) {}

    public static function rules(): array
    {
        return [
            'provider_payment_id' => ['required', 'string'],
            'status'  => ['required', 'string'], 
            'event_id' => ['required', 'string'],
        ];
    }
}
