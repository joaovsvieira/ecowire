<form x-on:submit.prevent="submit"
      x-data="{
            name: @entangle('accountForm.name').defer,
            cpf_cnpj: @entangle('accountForm.cpf_cnpj').defer,
            email: @entangle('accountForm.email').defer,

            async submit () {
                await $wire.callValidate()

                let errorCount = await $wire.getErrorCount()

               if (errorCount >= 1) {
                    return
               }

               await $wire.confirmPayment()
            },
      }"
>
    <div class="overflow-hidden sm:rounded-lg grid grid-cols-6 grid-flow-col gap-4">
        <div class="p-6 bg-white border-b border-gray-200 col-span-3 self-start space-y-6">
            <!-- Email -->
            @guest
                <div class="space-y-3">
                    <div class="font-semibold text-lg">Account details</div>
                    <div>
                        <label for="customerName">Name</label>
                        <x-input id="customerName" class="block mt-1 w-full" type="text" name="customerName" wire:model.defer="accountForm.name" />

                        @error ('accountForm.name')
                            <div class="mt-2 font-semibold text-red-500">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div>
                        <label for="customerCpfCnpj">CPF/CNPJ</label>
                        <x-input id="customerCpfCnpj" class="block mt-1 w-full" type="text" name="customerCpfCnpj" wire:model.defer="accountForm.cpf_cnpj" />

                        @error ('accountForm.cpf_cnpj')
                            <div class="mt-2 font-semibold text-red-500">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div>
                        <label for="email">Email</label>
                        <x-input id="email" class="block mt-1 w-full" type="email" name="email" wire:model.defer="accountForm.email" />

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
                    <div class="space-y-4 sm:flex sm:items-center sm:space-y-0 sm:space-x-10">
                        <div class="flex items-center">
                            <input id="card" value="card" wire:model="paymentType" type="radio" checked="" class="focus:ring-indigo-500 h-5 w-5 text-indigo-600 border-gray-300">
                            <label for="card" class="ml-3 block text-sm font-medium text-gray-700">
                                Cartão de crédito
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input id="pix" value="pix" wire:model="paymentType" type="radio" class="focus:ring-indigo-500 h-5 w-5 text-indigo-600 border-gray-300">
                            <label for="pix" class="ml-3 block text-sm font-medium text-gray-700">
                                PIX
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input id="boleto" value="boleto" wire:model="paymentType" type="radio" class="focus:ring-indigo-500 h-5 w-5 text-indigo-600 border-gray-300">
                            <label for="boleto" class="ml-3 block text-sm font-medium text-gray-700">
                                Boleto
                            </label>
                        </div>
                    </div>

                    @error ('paymentType')
                        <div class="mt-2 font-semibold text-red-500">
                            {{ $message }}
                        </div>
                    @enderror

                    @if ($paymentType == 'card')
                        <fieldset>
                            <div class="mt-4 bg-white rounded-md shadow-sm -space-y-px">
                                <div>
                                    <label for="cardNumber" class="sr-only">Card number</label>
                                    <input type="tel" wire:model.defer="cardElement.number" id="cardNumber" class="focus:ring-indigo-500 focus:border-indigo-500 relative block w-full rounded-none rounded-t-md bg-transparent focus:z-10 border-gray-300" placeholder="Card number">
                                </div>
                                <div class="flex -space-x-px">
                                    <div class="w-1/2 flex-1 min-w-0">
                                        <label for="cardExpiryMonth" class="sr-only">Expiration date</label>
                                        <input type="text" wire:model.defer="cardElement.expiry_month" id="cardExpiryMonth" class="focus:ring-indigo-500 focus:border-indigo-500 relative block w-full rounded-none rounded-bl-md bg-transparent focus:z-10 border-gray-300" placeholder="MM">
                                    </div>
                                    <div class="w-1/2 flex-1 min-w-0">
                                        <label for="cardExpiryYear" class="sr-only">Expiration date</label>
                                        <input type="text" wire:model.defer="cardElement.expiry_year" id="cardExpiryYear" class="focus:ring-indigo-500 focus:border-indigo-500 relative block w-full rounded-none rounded-bl-md bg-transparent focus:z-10 border-gray-300" placeholder="YYYY">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <label for="cardCvv" class="sr-only">CVV</label>
                                        <input type="tel" wire:model.defer="cardElement.cvv" id="cardCvv" class="focus:ring-indigo-500 focus:border-indigo-500 relative block w-full rounded-none rounded-br-md bg-transparent focus:z-10 border-gray-300" placeholder="CVV">
                                    </div>
                                </div>

                                @error ('cardElement.number')
                                    <div class="mt-2 font-semibold text-red-500">
                                        {{ $message }}
                                    </div>
                                @enderror

                                @error ('cardElement.expiry_month')
                                    <div class="mt-2 font-semibold text-red-500">
                                        {{ $message }}
                                    </div>
                                @enderror

                                @error ('cardElement.expiry_year')
                                    <div class="mt-2 font-semibold text-red-500">
                                        {{ $message }}
                                    </div>
                                @enderror

                                @error ('cardElement.cvv')
                                    <div class="mt-2 font-semibold text-red-500">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </fieldset>
                    @endif
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

                <x-button type="submit" wire:loading.attr="disabled">
                    <div wire:loading.inline wire:target="confirmPayment">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    Confirm order and pay
                </x-button>
            </div>
        </div>
    </div>
</form>
