<div class="bg-white shadow-lg rounded-lg p-6">
    <h2 class="text-xl font-semibold mb-4">Payment Details</h2>
    <form>
        <div class="mb-4">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="Auth::user()->name" readonly />
        </div>
        <div class="mb-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="Auth::user()->email" readonly />
        </div>
    </form>
</div>
