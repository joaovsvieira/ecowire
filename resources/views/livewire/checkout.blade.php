<form x-on:submit.prevent="submit"
      x-data="{
            email: @entangle('accountForm.email').defer,

            async submit () {
                await $wire.callValidate()

                let errorCount = await $wire.getErrorCount()

               if (errorCount >= 1) {
                    return
               }

               await $wire.confirmPayment(
                    '{{ $paymentIntent->uuid }}', {
                        payment_method: {
                            card: this.cardElement,
                            billing_details: { email: this.email }
                        }
                    }
               )
            },
      }"
>
    <div class="overflow-hidden sm:rounded-lg grid grid-cols-6 grid-flow-col gap-4">
        <div class="p-6 bg-white border-b border-gray-200 col-span-3 self-start space-y-6">
            @guest
                <div class="space-y-3">
                    <div class="font-semibold text-lg">Account details</div>

                    <div>
                        <label for="email">Email</label>
                        <x-input id="email" class="block mt-1 w-full" type="text" name="email" wire:model.defer="accountForm.email" />

                        @error ('accountForm.email')
                            <div class="mt-2 font-semibold text-red-500">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            @endguest

            <div class="space-y-3">
                <div class="font-semibold text-lg">Shipping</div>
                @if ($this->userShippingAddresses)
                    <x-select class="w-full" wire:model="userShippingAddressId">
                        <option value="">Choose a pre-saved address</option>
                        @foreach ($this->userShippingAddresses as $address)
                            <option value="{{ $address->id }}">{{ $address->formattedAddress() }}</option>
                        @endforeach
                    </x-select>
               @endif

                <div class="space-y-3">
                    <div>
                        <label for="address">Address</label>
                        <x-input id="address" class="block mt-1 w-full" type="text" name="address" wire:model.defer="shippingForm.address" />

                        @error ('shippingForm.address')
                            <div class="mt-2 font-semibold text-red-500">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-1">
                            <label for="city">City</label>
                            <x-input id="city" class="block mt-1 w-full" type="text" name="city" wire:model.defer="shippingForm.city" />

                            @error ('shippingForm.city')
                                <div class="mt-2 font-semibold text-red-500">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-span-1">
                            <label for="postcode">Postal code</label>
                            <x-input id="postcode" class="block mt-1 w-full" type="text" name="postcode" wire:model.defer="shippingForm.postcode" />

                            @error ('shippingForm.postcode')
                                <div class="mt-2 font-semibold text-red-500">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <div class="font-semibold text-lg">Delivery</div>

                <div class="space-y-1">
                    <x-select class="w-full" wire:model="shippingTypeId">
                        @foreach ($shippingTypes as $shippingType)
                            <option value="{{ $shippingType->id }}">{{ $shippingType->name }} ({{ $shippingType->formattedPrice() }})</option>
                        @endforeach
                    </x-select>
                </div>
            </div>

            <div class="space-y-3">
                <div class="font-semibold text-lg">Payment</div>

                <div>
                    {{ $paymentIntent->uuid }}
                    <fieldset>
                        <legend class="block text-sm font-medium text-gray-700">Card Details</legend>
                        <div class="mt-1 bg-white rounded-md shadow-sm -space-y-px">
                            <div>
                                <label for="card-number" class="sr-only">Card number</label>
                                <input type="text" name="card-number" id="card-number" class="focus:ring-indigo-500 focus:border-indigo-500 relative block w-full rounded-none rounded-t-md bg-transparent focus:z-10 sm:text-sm border-gray-300" placeholder="Card number">
                            </div>
                            <div class="flex -space-x-px">
                                <div class="w-1/2 flex-1 min-w-0">
                                    <label for="card-expiration-date" class="sr-only">Expiration date</label>
                                    <input type="text" name="card-expiration-date" id="card-expiration-date" class="focus:ring-indigo-500 focus:border-indigo-500 relative block w-full rounded-none rounded-bl-md bg-transparent focus:z-10 sm:text-sm border-gray-300" placeholder="MM / YY">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <label for="card-cvc" class="sr-only">CVC</label>
                                    <input type="text" name="card-cvc" id="card-cvc" class="focus:ring-indigo-500 focus:border-indigo-500 relative block w-full rounded-none rounded-br-md bg-transparent focus:z-10 sm:text-sm border-gray-300" placeholder="CVC">
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white border-b border-gray-200 col-span-3 self-start space-y-4">
            <div>
                @foreach ($cart->contents() as $variation)
                    <div class="border-b py-3 flex items-start">
                        <div class="w-16 mr-4">
                            <img src="{{ $variation->getFirstMediaUrl('default', 'thumb200x200') }}" class="w-16">
                        </div>

                        <div class="space-y-2">
                            <div>
                                <div class="font-semibold">
                                    {{ $variation->formattedPrice() }}
                                </div>
                                <div class="space-y-1">
                                    <div>{{ $variation->product->name }}</div>

                                    <div class="flex items-center text-sm">
                                        <div class="mr-1 font-semibold">
                                            Quantity: {{ $variation->pivot->quantity }} <span class="text-gray-400 mx-1">/</span>
                                        </div>
                                        @foreach ($variation->ancestorsAndSelf as $ancestor)
                                            {{ $ancestor->name }} @if (!$loop->last) <span class="text-gray-400 mx-1">/</span> @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="space-y-4">
                <div class="space-y-1">
                    <div class="space-y-1 flex items-center justify-between">
                        <div class="font-semibold">Subtotal</div>
                        <h1 class="font-semibold">{{ $cart->formattedSubtotal() }}</h1>
                    </div>

                    <div class="space-y-1 flex items-center justify-between">
                        <div class="font-semibold">Shipping ({{ $this->shippingType->name }})</div>
                        <h1 class="font-semibold">{{ $this->shippingType->formattedPrice() }}</h1>
                    </div>

                    <div class="space-y-1 flex items-center justify-between">
                        <div class="font-semibold">Total</div>
                        <h1 class="font-semibold">{{ money($this->total) }}</h1>
                    </div>
                </div>

                <x-button type="submit">Confirm order and pay</x-button>
            </div>
        </div>
    </div>
</form>
