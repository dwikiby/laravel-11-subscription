<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pricing') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-center text-3xl font-bold mb-8">Our Pricing Plans</h1>
                    <div class="flex flex-wrap -mx-2">
                        @foreach($plans as $plan)
                            <div class="w-full md:w-1/3 px-4 mb-4">
                                <div class="bg-white rounded-lg shadow-lg border">
                                    <div class="bg-red-500 p-4 rounded-t-lg text-white">
                                        <h4 class="text-xl font-semibold">{{ $plan->name }}</h4>
                                    </div>
                                    <div class="p-4">
                                        <h1 class="text-2xl font-bold mb-2">{{ formatRupiah($plan->price) }} <small class="text-red-500">/ month</small></h1>
                                        <ul class="list-none mt-3 mb-4">
                                            <li>{{ $plan->description }}</li>
                                        </ul>
                                        <div class="flex justify-center">
                                            @if($purchasedPlans->contains($plan->id))
                                                {{-- <span class="bg-gray-500 text-white font-bold py-2 px-4 rounded-lg w-full text-center">
                                                    Already Purchased
                                                </span> --}}
                                                <a href="{{ route('plans.show', $plan) }}" class="bg-gray-800 text-white font-bold py-2 px-4 rounded-lg w-full text-center">
                                                    Already Purchased
                                                </a>
                                            @else
                                                <a href="{{ route('plans.show', $plan) }}" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg w-full text-center">
                                                    Get Started
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
