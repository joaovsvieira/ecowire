<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PaymentIntent extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_CANCELED = 'canceled';
    const STATUS_PROCESSING = 'processing';
    const STATUS_REQUIRES_ACTION = 'requires_action';
    const STATUS_REQUIRES_CAPTURE = 'requires_capture';
    const STATUS_REQUIRES_CONFIRMATION = 'requires_confirmation';
    const STATUS_REQUIRES_PAYMENT_METHOD = 'requires_payment_method';
    const STATUS_SUCCEEDED = 'succeeded';

    protected $fillable = [
        'ipag_id',
        'amount',
        'order_id',
        'callback_url',
        'payment_type',
        'payment_method',
        'payment_installments',
        'payment_capture',
        'payment_fraud_analysis',
        'payment_softdescriptor',
        'payment_pix_expires_in',
        'card_holder',
        'card_number',
        'card_expiry_month',
        'card_expiry_year',
        'card_cvv',
        'card_token',
        'card_tokenize',
        'boleto_due_date',
        'boleto_instructions',
        'customer_name',
        'customer_cpf_cnpj',
        'customer_email',
        'customer_phone',
        'customer_birthdate',
        'customer_ip',
        'status',
        'pix_url',
        'metadata',
    ];

    protected $casts = [
        'boleto_instructions' => 'array',
        'metadata' => 'json',
    ];

    public static function booted()
    {
        static::creating(function (PaymentIntent $paymentIntent) {
            $paymentIntent->uuid = (string) Str::uuid();
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function getFormattedAttributes()
    {
        return [
            // TODO: encontrar uma forma de deixar a currency dinamica
            'ipag_id' => $this->ipag_id,
            'amount'  => floatval(str_replace('R$', '', money($this->amount))),
            'order_id'  => $this->order_id,
            'callback_url'  => $this->callback_url,
            'payment' => [
                'type'   => $this->payment_type,
                'method' => $this->payment_method,
                'installments' => $this->payment_installments,
                'capture' => $this->payment_capture,
                'fraud_analysis' => $this->payment_fraud_analysis,
                'softdescriptor' => $this->payment_softdescriptor,
                'pix_expires_in' => $this->payment_pix_expires_in,
                'card' => [
                    'holder' => $this->card_holder,
                    'number' => $this->card_number,
                    'expiry_month' => $this->card_expiry_month,
                    'expiry_year'  => $this->card_expiry_year,
                    'cvv' => $this->card_cvv,
                    'token' => $this->card_token,
                    'tokenize' => $this->card_tokenize,
                ],
                'boleto' => [
                    'due_date'     => $this->boleto_due_date,
                    'instructions' => $this->boleto_instructions,
                ]
            ],
            'customer' => [
                'name' => $this->customer_name,
                'cpf_cnpj' => $this->customer_cpf_cnpj,
                'email' => $this->customer_email,
                'phone' => $this->customer_phone,
                'birthdate' => $this->customer_birthdate,
                'ip' => $this->customer_ip,
            ],
            'status'   => $this->status,
            'pix_url'  => $this->pix_url,
        ];
    }
}
