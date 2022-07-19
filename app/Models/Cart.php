<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_intent_id',
    ];

    public static function booted()
    {
        static::creating(function (Cart $cart) {
            $cart->uuid = (string) Str::uuid();
        });
    }

    public function paymentIntent()
    {
        return $this->belongsTo(PaymentIntent::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function variations()
    {
        return $this->belongsToMany(Variation::class)
                ->withPivot('quantity')
                ->orderBy('id');
    }
}
