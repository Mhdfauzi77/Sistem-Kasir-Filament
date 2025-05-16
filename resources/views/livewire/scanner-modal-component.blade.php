<div x-data="{ open: false }"
     x-on:toggle-scanner.window="open = true; startScanner()"
     x-show="open"
     style="display: none"
     class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">

    <div class="bg-white p-4 rounded shadow-lg w-[320px]">
        <h2 class="text-lg font-semibold mb-2">Scan Barcode</h2>
        <div id="reader" style="width: 300px; height: 300px;"></div>
        <button @click="open = false" class="mt-4 bg-red-500 text-white px-4 py-2 rounded">Tutup</button>
    </div>

    {{-- ✅ Tambah script QRCode --}}
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        function startScanner() {
            const html5QrCode = new Html5Qrcode("reader");
            const config = { fps: 10, qrbox: 250 };

            html5QrCode.start(
                { facingMode: "environment" },
                config,
                (decodedText) => {
                    html5QrCode.stop(); // stop kamera setelah scan
                    Livewire.emit('barcodeScanned', decodedText); // kirim ke Livewire
                },
                (errorMessage) => {
                    // error bisa diabaikan agar tidak spam console
                }
            ).catch((err) => {
                console.error("Scanner gagal jalan: ", err);
            });
        }
    </script>
</div>
