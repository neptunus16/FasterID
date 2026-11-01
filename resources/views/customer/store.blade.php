@extends('layouts.app')
@section('title', $store->store_name)
@section('content')

<div class="mb-6">
    <a href="{{ route('customer.dashboard') }}" class="text-sm text-orange-500 hover:underline">← Kembali</a>
    <h1 class="text-2xl font-bold text-gray-800 mt-2">🏪 {{ $store->store_name }}</h1>
    <p class="text-gray-400 text-sm mt-1">📍 {{ $store->address }}</p>
</div>

@if($menus->isEmpty())
<div class="text-center py-20 text-gray-400">
    <div class="text-5xl mb-3">🍽️</div>
    <p>Belum ada menu tersedia di toko ini.</p>
</div>
@else

<form method="POST" action="{{ route('customer.order.place', $store) }}" id="orderForm">
    @csrf
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8" id="menuGrid">
        @foreach($menus as $menu)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
            @if($menu->image)
                <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-full h-40 object-cover">
            @else
                <div class="w-full h-40 bg-orange-50 flex items-center justify-center text-5xl">🍱</div>
            @endif
            <div class="p-4 flex flex-col flex-1">
                <h3 class="font-semibold text-gray-800">{{ $menu->name }}</h3>
                @if($menu->description)<p class="text-xs text-gray-400 mt-1">{{ $menu->description }}</p>@endif
                <p class="text-orange-500 font-bold mt-2">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                <div class="mt-3 flex items-center gap-2">
                    <label class="text-xs text-gray-500">Qty:</label>
                    <input type="number" name="items[{{ $loop->index }}][quantity]" value="0" min="0" max="99"
                        data-price="{{ $menu->price }}"
                        class="w-16 border border-gray-200 rounded-lg px-2 py-1 text-sm text-center focus:outline-none focus:ring-2 focus:ring-orange-400 qty-input">
                    <input type="hidden" name="items[{{ $loop->index }}][menu_id]" value="{{ $menu->id }}">
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Delivery Info & Ringkasan Biaya --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4 max-w-xl">
        <h2 class="font-semibold text-gray-700">📋 Info Pengiriman & Pembayaran</h2>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Pengiriman <span class="text-red-500">*</span></label>
            <textarea name="delivery_address" rows="2" required placeholder="Masukkan alamat lengkap pengantaran..."
                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">{{ old('delivery_address') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Pesanan (opsional)</label>
            <input type="text" name="customer_note" placeholder="Contoh: jangan terlalu pedas, tambah sendok"
                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
            <div class="flex gap-4">
                <label class="flex items-center gap-2 cursor-pointer text-sm">
                    <input type="radio" name="payment_method" value="cash" checked class="text-orange-500"> 💵 Tunai (Cash on Delivery)
                </label>
                <label class="flex items-center gap-2 cursor-pointer text-sm">
                    <input type="radio" name="payment_method" value="transfer" class="text-orange-500"> 💳 Transfer Bank
                </label>
            </div>
        </div>

        {{-- Ringkasan Perhitungan Biaya --}}
        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 text-sm space-y-2">
            <div class="flex justify-between text-gray-600">
                <span>Subtotal Menu:</span>
                <span id="subtotalDisplay" class="font-semibold">Rp 0</span>
            </div>
            <div class="flex justify-between text-gray-600">
                <span>Ongkos Kirim (Flat):</span>
                <span class="font-semibold text-gray-700">Rp 8.000</span>
            </div>
            <div class="border-t border-gray-200 pt-2 flex justify-between font-bold text-base text-gray-900">
                <span>Total Pembayaran:</span>
                <span id="totalDisplay" class="text-orange-600 font-extrabold">Rp 8.000</span>
            </div>
        </div>

        <button type="submit" id="orderBtn"
            class="w-full bg-orange-500 hover:bg-orange-600 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold py-3 rounded-xl transition text-sm shadow-sm">
            🛒 Buat Pesanan Sekarang
        </button>
    </div>
</form>

@push('scripts')
<script>
    const shippingFee = 8000;
    const qtyInputs = document.querySelectorAll('.qty-input');
    const subtotalDisplay = document.getElementById('subtotalDisplay');
    const totalDisplay = document.getElementById('totalDisplay');

    function calculateTotal() {
        let subtotal = 0;
        qtyInputs.forEach(input => {
            const qty = parseInt(input.value) || 0;
            const price = parseFloat(input.dataset.price) || 0;
            subtotal += (qty * price);
        });

        const grandTotal = subtotal > 0 ? (subtotal + shippingFee) : 0;
        subtotalDisplay.innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
        totalDisplay.innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');
    }

    qtyInputs.forEach(input => {
        input.addEventListener('input', calculateTotal);
        input.addEventListener('change', calculateTotal);
    });

    document.getElementById('orderForm').addEventListener('submit', function(e) {
        let hasItem = false;
        qtyInputs.forEach(i => { if (parseInt(i.value) > 0) hasItem = true; });
        if (!hasItem) {
            e.preventDefault();
            alert('Pilih minimal 1 item menu yang ingin dipesan (Qty > 0).');
        }
    });

    calculateTotal();
</script>
@endpush

@endif

@endsection
