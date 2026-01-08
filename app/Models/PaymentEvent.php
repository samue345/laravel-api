<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentEvent extends Model
{
      protected $table = 'payment_events';

      protected $fillable = [
        'payment_id',
        'provider',
        'provider_event_id',
        'payload',
      ];

      protected $casts = [
        'payload' => 'array',
      ];
}
