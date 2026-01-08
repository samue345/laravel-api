<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Payment extends Model
{
      use HasFactory;

      protected $table = 'payments';

      protected $fillable = [
        'user_id',
        'amount',
        'currency',
        'provider',
        'provider_payment_id',
        'status',
        'idempotency_key',
     ];

     public function user() {
        return $this->belongsTo(User::class);
     }
}
