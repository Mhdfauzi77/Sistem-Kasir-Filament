<div>  {{-- ROOT ELEMENT PEMBUKA --}}

    <div class="grid grid-cols-1 dark:bg-gray-900 md:grid-cols-3 gap-4">
        <div class="md:col-span-2 bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <div class="mb-4 flex gap-2">
                <input wire:model.live.debounce.300ms='search' type="text" placeholder="Cari produk..."
                    class="w-full p-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">

                {{-- ✅ PERBAIKI DISINI: --}}
                <x-filament::button x-data x-on:click="$dispatch('toggle-scanner')" color="primary">
                    Scan Barcode
                </x-filament::button>
            </div>

            <livewire:scanner-modal-component />

            <div class="flex-grow mt-4">
                <div class="grid grid-cols-8 sm:grid-cols-3 md:grid-cols-8 lg:grid-cols- gap-4">
                    @foreach ($products as $item)
                    <div wire:click="addToOrder({{ $item->id }})" class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow cursor-pointer">
                        <img src="{{ $item->image_url }}"
                            alt="Product Image" class="w-full h-16 object-cover rounded-lg mb-2">
                        <h3 class="text-sm font-semibold">{{ $item->name }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-xs">Rp. {{ number_format($item->price, 0, ',', '.') }}</p>
                        <p class="text-gray-600 dark:text-gray-400 text-xs">Stok: {{ $item->stock }}</p>
                    </div>
                    @endforeach
                </div>
                <div class="py-4">
                    {{ $products->links() }}
                </div>
            </div>
        </div>

        <div class="md:col-span-1 bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <div class="py-4">
                <h3 class="text-lg font-semibold text-center">Total: Rp {{ number_format($total_price, 0, ',', '.') }}</h3>
            </div>

            @foreach($order_items as $item)
            <div class="mb-4">
                <div class="flex justify-between items-center bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow">
                    <div class="flex items-center">
                        <img src="{{ $item['image'] }}" alt="Product Image" class="w-10 h-10 object-cover rounded-lg mr-2">
                        <div class="px-2">
                            <h3 class="text-sm font-semibold">{{ $item['name'] }}</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-xs">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <x-filament::button color="warning" wire:click="decreaseQuantity({{ $item['product_id'] }})">-</x-filament::button>
                        <span class="px-4">{{ $item['quantity'] }}</span>
                        <x-filament::button color="success" wire:click="increaseQuantity({{ $item['product_id'] }})">+</x-filament::button>
                    </div>
                </div>
            </div>
            @endforeach

            <form wire:submit.prevent="checkout">
                {{ $this->form }}
                <div class="flex justify-between items-center mt-4">
                    <x-filament::button type="submit" class="w-full bg-red-500 mt-3 text-white py-2 rounded">
                        Checkout
                    </x-filament::button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode"></script>
</div>  {{-- ROOT ELEMENT PENUTUP --}}
