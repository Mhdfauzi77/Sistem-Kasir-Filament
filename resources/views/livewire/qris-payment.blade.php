<div class="flex flex-col items-center justify-center h-screen">
    <h1 class="text-2xl font-bold mb-4">Silakan Scan QRIS untuk Membayar</h1>
    <img src="{{ asset('images/qris-placeholder.png') }}" alt="QRIS QR Code" class="w-64 h-64 mb-4">
    <button wire:click="backToPos" class="bg-blue-600 text-white px-4 py-2 rounded">
        Selesai Pembayaran
    </button>
</div>
