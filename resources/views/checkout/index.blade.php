<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Checkout for Plan {{ $plan->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Plan Details -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-2xl font-bold">{{ $plan->name }} Plan</h2>
                <p class="text-gray-600 mt-2">Get access to all the features and tools for your business.</p>
                <div class="text-3xl font-bold mt-4">
                    {{ formatRupiah($plan->price) }}
                    <small class="text-lg">/month</small>
                </div>
                <ul class="mt-4 list-disc pl-6 space-y-2">
                    <li>{{ $plan->description }}</li>
                </ul>
            </div>
             <!-- Payment Form -->
             <div>
                @if($existingSubscription)
                    <div class="bg-green-100 text-green-700 p-4 rounded-lg">
                        You have already purchased this plan.
                    </div>
                @else
                    <form method="POST" action="{{ route('checkout', $plan) }}">
                        @csrf
                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">

                        <!-- Order Summary -->
                        <div class="bg-white shadow-lg rounded-lg p-6 mt-6">
                            <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
                            <div class="flex justify-between items-center mb-4">
                                <span>{{ $plan->name }} Plan</span>
                                <span>{{ formatRupiah($plan->price) }}/month</span>
                            </div>
                            <div class="flex justify-between items-center mb-4">
                                <span>Total</span>
                                <span class="font-bold">{{ formatRupiah($plan->price) }}/month</span>
                            </div>
                            <div class="mb-2">
                                <x-primary-button type="submit" class="w-full justify-center">
                                    {{ __('Checkout') }}
                                </x-primary-button>
                            </div>
                            <div>
                                <a href="{{ route('pricing.index') }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-slate-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">Cancel</a>
                            </div>
                        </div>
                    </form>
                    @if (request()->has('snap_token'))
                    <div class="bg-white shadow-lg rounded-lg p-6 mt-6">
                        <h2 class="text-xl font-semibold mb-4">Complete Your Payment</h2>
                        <div id="payment-form"></div>
                    </div>

                    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
                    <script type="text/javascript">
                        document.addEventListener('DOMContentLoaded', function () {
                            snap.pay('{{ request('snap_token') }}', {
                                onSuccess: function (result) {
                                    console.log(result);
                                    window.location.href = '{{ route("subscription.success", ["subscription" => request()->subscription_id]) }}';
                                },
                                onPending: function (result) {
                                    console.log(result);
                                },
                                onError: function (result) {
                                    console.log(result);
                                },
                            });
                        });
                    </script>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
