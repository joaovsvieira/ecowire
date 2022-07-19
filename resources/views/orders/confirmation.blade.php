<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-sl text-gray-800 leading-tight">
            Thanks for your order
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if ($order->presenter()->status() == null)

                        @if ($order->payment->payment_type == 'pix')
                            <img src="https://chart.googleapis.com/chart?chs=500x500&cht=qr&chl={{ $order->payment->pix_url }}" alt="">
                        @else
                            BOLETO
                        @endif

                    @else
                        Your order (#{{ $order->id }}) has been placed.

                        <a href="/register" class="text-indigo-500">Create an account</a> to manage your orders.
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
