<?php

namespace App\Http\Livewire;

use App\Cart\Contracts\CartInterface;
use App\Events\Payments\CapturePayment;
use App\Mail\OrderCreated;
use App\Models\Order;
use App\Models\PaymentIntent;
use App\Models\ShippingAddress;
use App\Models\ShippingType;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Checkout extends Component
{
    public $shippingTypes, $shippingTypeId, $userShippingAddressId;
    public $paymentType;
    public $cardElement  = [
        'number' => '',
        'expiry_month' => '',
        'expiry_year' => '',
        'cvv' => '',
    ];
    public $accountForm  = [
        'name' => '',
        'cpf_cnpj' => '',
        'email' => '',
    ];
    public $shippingForm = [
        'address' => '',
        'city' => '',
        'postcode' => '',
    ];
    protected $shippingAddress;

    protected $validationAttributes = [
        'paymentType' => 'payment type',
        'cardElement.number' => 'card number',
        'cardElement.expiry_month' => 'card expiry month',
        'cardElement.expiry_year' => 'card expiry year',
        'cardElement.cvv' => 'card cvv',
        'accountForm.name' => 'customer name',
        'accountForm.cpf_cnpj' => 'customer document',
        'accountForm.email' => 'email address',
        'shippingForm.address' => 'shipping address',
        'shippingForm.city' => 'shipping city',
        'shippingForm.postcode' => 'shipping postal code',
    ];
    protected $messages = [
        'cardElement.number.required' => 'Your :attribute is required.',
        'cardElement.expiry_month.required' => 'Your :attribute is required.',
        'cardElement.expiry_year.required' => 'Your :attribute is required.',
        'cardElement.cvv.required' => 'Your :attribute is required.',
        'accountForm.name.required' => 'Your :attribute is required.',
        'accountForm.cpf_cnpj.required' => 'Your :attribute is required.',
        'accountForm.email.unique' => 'Seems you already have an account. Please sign in to place an order.',
        'shippingForm.address.required' => 'Your :attribute is required.',
        'shippingForm.city.required' => 'Your :attribute is required.',
        'shippingForm.postcode.required' => 'Your :attribute is required.',
        'paymentType.required' => 'Your :attribute is required.',
    ];

    public function rules()
    {
        return [
            'cardElement.number' => 'required_if:paymentType,card',
            'cardElement.expiry_month' => 'required_if:paymentType,card',
            'cardElement.expiry_year' => 'required_if:paymentType,card',
            'cardElement.cvv' => 'required_if:paymentType,card',
            'accountForm.name' => 'required|string|min:5|max:255',
            'accountForm.cpf_cnpj' => 'required|string|max:255',
            'accountForm.email' => 'required|email|max:255|unique:users,email' . (auth()->user() ? ',' . auth()->user()->id : ''),
            'shippingForm.address' => 'required|max:255',
            'shippingForm.city' => 'required|max:255',
            'shippingForm.postcode' => 'required|max:255',
            'shippingTypeId' => 'required|exists:shipping_types,id',
            'paymentType' => 'required',
        ];
    }

    public function callValidate()
    {
        $this->validate();
    }

    public function getErrorCount()
    {
        return $this->getErrorBag()->count();
    }

    public function updatedUserShippingAddressId($id)
    {
        if (!$id) {
            return;
        }

        $this->shippingForm = $this->userShippingAddresses->find($id)->only('address', 'city', 'postcode');
    }

    public function getUserShippingAddressesProperty()
    {
        return auth()->user()?->shippingAddresses;
    }

    public function getShippingTypeProperty()
    {
        return $this->shippingTypes->find($this->shippingTypeId);
    }

    public function getTotalProperty(CartInterface $cart)
    {
        return $cart->subtotal() + $this->shippingType->price;
    }

    /**
     * Realiza a ação de pagamento
     */
    public function confirmPayment(CartInterface $cart)
    {
        $paymentIntent = $this->getPaymentIntent($cart);

        $response = app('ipag')->paymentIntents($paymentIntent->getFormattedAttributes());

        if (isset($response['id'])) {
            $paymentIntent->update([
                'ipag_id' => $response['id'],
                'status'  => $paymentIntent->payment_type == 'card' ? 'requires_capture' :
                        ($paymentIntent->payment_type == 'pix' ? 'requires_confirmation' :
                            ($paymentIntent->payment_type == 'boleto' ? 'requires_confirmation' : 'requires_payment_method')
                        ),
                'pix_url' => $paymentIntent->payment_type == 'pix' ? $response['attributes']['pix']['qrcode'] : null,
            ]);

            return $this->checkout($cart);
        } else {
            $this->dispatchBrowserEvent('notification', [
                'body' => 'Your payment failed.',
            ]);

            return;
        }
    }

    /**
     * Instancia a intenção de pagamento
     */
    public function getPaymentIntent(CartInterface $cart)
    {
        if ($cart->hasPaymentIntent()) {
           $paymentIntent = PaymentIntent::find($cart->getPaymentIntentId());

           if ($paymentIntent->status != 'succeeded' OR $paymentIntent->status != 'requires_capture' OR $paymentIntent->status != 'processing') {
                $paymentIntent->update([
                    'amount'          => $this->total,

                    'payment_type'    => $this->paymentType,
                    'payment_method'  => $this->paymentType == 'card' ? 'visa' : ($this->paymentType == 'pix' ? 'pix' : ($this->paymentType == 'boleto' ? 'boleto' : null)),

                    'card_holder' => $this->paymentType == 'card' ? $this->accountForm['name'] : null,
                    'card_number' => $this->paymentType == 'card' ? $this->cardElement['number'] : null,
                    'card_expiry_month' => $this->paymentType == 'card' ? $this->cardElement['expiry_month'] : null,
                    'card_expiry_year'  => $this->paymentType == 'card' ? $this->cardElement['expiry_year'] : null,
                    'card_cvv' => $this->paymentType == 'card' ? $this->cardElement['cvv'] : null,

                    'boleto_due_date' => $this->paymentType == 'boleto' ? date('Y-m-d', strtotime(now()->addDays(5))) : null,
                    'boleto_instructions' => [
                        'Sr. Caixa não receber após o vencimento',
                    ],

                    'customer_name'     => $this->accountForm['name'],
                    'customer_cpf_cnpj' => $this->accountForm['cpf_cnpj'],
                ]);
           }

           return $paymentIntent;
        }

        $paymentIntent = PaymentIntent::create([
            'amount'          => $this->total,

            'payment_type'    => $this->paymentType,
            'payment_method'  => $this->paymentType == 'card' ? 'visa' : ($this->paymentType == 'pix' ? 'pix' : ($this->paymentType == 'boleto' ? 'boleto' : null)),

            'card_holder' => $this->paymentType == 'card' ? $this->accountForm['name'] : null,
            'card_number' => $this->paymentType == 'card' ? $this->cardElement['number'] : null,
            'card_expiry_month' => $this->paymentType == 'card' ? $this->cardElement['expiry_month'] : null,
            'card_expiry_year'  => $this->paymentType == 'card' ? $this->cardElement['expiry_year'] : null,
            'card_cvv' => $this->paymentType == 'card' ? $this->cardElement['cvv'] : null,

            'boleto_due_date' => $this->paymentType == 'boleto' ? now()->addDays(5) : null,
            'boleto_instructions' => [
                'Sr. Caixa não receber após o vencimento',
            ],

            'customer_name'     => $this->accountForm['name'],
            'customer_cpf_cnpj' => $this->accountForm['cpf_cnpj'],

            'status' => 'requires_action',
        ]);

        $cart->updatePaymentIntent($paymentIntent->id);

        return $paymentIntent;
    }

    /**
     * Ações pós pagamento
     */
    public function checkout(CartInterface $cart)
    {
        $this->validate();

        if (!$this->getPaymentIntent($cart)->status === 'requires_capture' OR !$this->getPaymentIntent($cart)->status === 'succeeded' OR !$this->getPaymentIntent($cart)->status === 'requires_confirmation') {
            $this->dispatchBrowserEvent('notification', [
                'body' => 'Your payment failed.',
            ]);

            return;
        }

        $this->shippingAddress = ShippingAddress::query();

        if (auth()->user()) {
            $this->shippingAddress = $this->shippingAddress->whereBelongsTo(auth()->user());
        }

        ($this->shippingAddress = $this->shippingAddress->firstOrCreate($this->shippingForm))
            ?->user()
            ->associate(auth()->user())
            ->save();

        $order = Order::make(array_merge($this->accountForm, [
            'subtotal' => $cart->subtotal(),
        ]));

        $order->user()->associate(auth()->user());

        $order->shippingType()->associate($this->shippingType);
        $order->shippingAddress()->associate($this->shippingAddress);

        $order->save();

        $order->variations()->attach(
            $cart->contents()->mapWithKeys(function ($variation) {
                return [
                    $variation->id => [
                        'quantity' => $variation->pivot->quantity,
                    ],
                ];
            })
            ->toArray()
        );

        $paymentIntent = PaymentIntent::find($cart->getPaymentIntentId());
        $paymentIntent->update([
            'order_id' => $order->id,
        ]);

        $cart->contents()->each(function ($variation) {
            $variation->stocks()->create([
                'amount' => 0 - $variation->pivot->quantity,
            ]);
        });

        $cart->removeAll();

        $cart->destroy();

        if ($this->getPaymentIntent($cart)->status == 'requires_capture') {
            event(new CapturePayment($order));

            if (!auth()->user()) {
                return redirect()->route('orders.confirmation', $order);
            }

            return redirect()->route('orders');

        }

        return redirect()->route('orders.confirmation', $order);
    }

    public function mount()
    {
        $this->shippingTypes = ShippingType::orderBy('price', 'asc')->get();
        $this->shippingTypeId = $this->shippingTypes->first()->id;

        if ($user = auth()->user()) {
            $this->accountForm['name'] = $user->name;
            $this->accountForm['cpf_cnpj'] = $user->cpf_cnpj;
            $this->accountForm['email'] = $user->email;
        }
    }

    public function render(CartInterface $cart)
    {
        return view('livewire.checkout', [
            'cart' => $cart,
            'paymentIntent' => $this->getPaymentIntent($cart),
        ]);
    }
}
