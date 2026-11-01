@extends('layouts.app')
@section('title', 'Dashboard Driver')
@section('content')

<div class="mb-6 flex items-center justify-between flex-wrap gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">🏍️ Halo, {{ $driver->name }}!</h1>
        <p class="text-gray-500 text-sm mt-0.5 flex items-center gap-2">
            Status Kesiapan:
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold {{ $driver->driverStatusColor() }}">
                {{ $driver->driverStatusIcon() }} {{ $driver->driverStatusLabel() }}
            </span>
        </p>
    </div>

    {{-- Pilihan Status Driver (Online, Resting, Offline) --}}
    <div class="flex items-center bg-white p-1 rounded-xl shadow-sm border border-gray-200 gap-1">
        <form method="POST" action="{{ route('driver.set-status') }}">
            @csrf
            <input type="hidden" name="driver_status" value="online">
            <button type="submit"
                class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1
                {{ $driver->driver_status === 'online' ? 'bg-green-500 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                🟢 Aktif
            </button>
        </form>

        <form method="POST" action="{{ route('driver.set-status') }}">
            @csrf
            <input type="hidden" name="driver_status" value="resting">
            <button type="submit"
                class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1
                {{ $driver->driver_status === 'resting' ? 'bg-yellow-400 text-gray-900 shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                🟡 Istirahat
            </button>
        </form>

        <form method="POST" action="{{ route('driver.set-status') }}">
            @csrf
            <input type="hidden" name="driver_status" value="offline">
            <button type="submit"
                class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1
                {{ $driver->driver_status === 'offline' ? 'bg-gray-700 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                ⚫ Offline
            </button>
        </form>
    </div>
</div>

{{-- Earnings card --}}
<div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl p-6 mb-8 flex items-center gap-6 shadow-sm">
    <div class="text-5xl">💰</div>
    <div>
        <p class="text-orange-100 text-sm font-medium">Penghasilan Hari Ini</p>
        <p class="text-3xl font-extrabold">Rp {{ number_format($todayEarnings, 0, ',', '.') }}</p>
    </div>
</div>

{{-- Active Order --}}
@if($activeOrder)
<div class="bg-white border-l-4 {{ $activeOrder->is_custom ? 'border-purple-500' : 'border-orange-500' }} rounded-xl shadow-sm p-6 mb-8">
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
            <h2 class="font-bold text-gray-800">📦 Pesanan Sedang Berjalan — #{{ $activeOrder->id }}</h2>
            @if($activeOrder->is_custom)
                <span class="text-[10px] bg-purple-100 text-purple-700 font-bold px-2 py-0.5 rounded-full">🛍️ Toko Non-Mitra</span>
            @endif
        </div>
        <span class="text-xs px-2.5 py-1 rounded-full font-bold uppercase bg-orange-100 text-orange-700">
            {{ str_replace('_', ' ', $activeOrder->status) }}
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-4">
        <div>
            <span class="text-gray-400 text-xs block">Pelanggan</span>
            <p class="font-medium text-gray-800">{{ $activeOrder->customer->name }} (📞 {{ $activeOrder->customer->phone ?? '-' }})</p>
        </div>
        <div>
            <span class="text-gray-400 text-xs block">Toko / Lokasi Pembelian</span>
            <p class="font-medium text-gray-800">{{ $activeOrder->display_store_name }}</p>
            @if($activeOrder->display_store_address)
                <p class="text-xs text-gray-500 mt-0.5">📍 {{ $activeOrder->display_store_address }}</p>
            @endif
        </div>
        <div>
            <span class="text-gray-400 text-xs block">Alamat Pengantaran</span>
            <p class="font-medium text-gray-800">{{ $activeOrder->delivery_address }}</p>
        </div>
        <div>
            <span class="text-gray-400 text-xs block">Rincian Biaya</span>
            @if($activeOrder->is_custom)
                <p class="font-bold text-purple-700">
                    Belanja: {{ $activeOrder->shopping_cost > 0 ? 'Rp ' . number_format($activeOrder->shopping_cost, 0, ',', '.') : 'Belum diisi' }}
                    + Ongkir: Rp {{ number_format($activeOrder->shipping_fee ?? 10000, 0, ',', '.') }}
                </p>
            @else
                <p class="font-bold text-orange-600">Rp {{ number_format($activeOrder->grand_total, 0, ',', '.') }}</p>
            @endif
        </div>
    </div>

    @if($activeOrder->is_custom && $activeOrder->notes)
    <div class="mb-4 p-3.5 bg-purple-50 rounded-xl border border-purple-100 text-xs">
        <span class="font-bold text-purple-900 block mb-1">📝 Catatan / Daftar Pesanan Yang Harus Dibelikan:</span>
        <p class="text-purple-950 font-mono whitespace-pre-line">{{ $activeOrder->notes }}</p>
    </div>
    @endif

    <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-gray-100">
        @if($activeOrder->status === 'processing')
        <form method="POST" action="{{ route('driver.update-status', $activeOrder) }}">
            @csrf
            <input type="hidden" name="status" value="delivering">
            <button class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition flex items-center gap-1.5 shadow-sm">
                🚀 Mulai Mengantar
            </button>
        </form>
        @elseif($activeOrder->status === 'delivering')
            @if($activeOrder->is_custom)
                {{-- Tombol Buka Modal Input Belanjaan untuk Pesanan Custom --}}
                <button type="button" onclick="document.getElementById('completeCustomModal').classList.remove('hidden')"
                    class="bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition flex items-center gap-1.5 shadow-sm">
                    ✅ Selesaikan Pesanan (Input Belanjaan)
                </button>
            @else
                {{-- Selesaikan Pesanan Reguler --}}
                <form method="POST" action="{{ route('driver.update-status', $activeOrder) }}">
                    @csrf
                    <input type="hidden" name="status" value="completed">
                    <button class="bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition flex items-center gap-1.5 shadow-sm">
                        ✅ Selesai Diantar
                    </button>
                </form>
            @endif
        @endif
    </div>

    {{-- Modal Pop-up Input Belanjaan Riil untuk Driver (Pesanan Custom) --}}
    @if($activeOrder->is_custom)
    <div id="completeCustomModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                <h3 class="font-bold text-gray-800 text-sm">🧾 Input Biaya Belanja Riil</h3>
                <button type="button" onclick="document.getElementById('completeCustomModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-lg">✕</button>
            </div>
            <form method="POST" action="{{ route('driver.update-status', $activeOrder) }}" class="space-y-4">
                @csrf
                <input type="hidden" name="status" value="completed">
                <p class="text-xs text-gray-500">
                    Masukkan total harga belanjaan sesuai nota/struk pembelian di <strong>{{ $activeOrder->store_name }}</strong>.
                </p>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">
                        Total Belanjaan Riil (Rp) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="shopping_cost" required min="1000" step="500" placeholder="Contoh: 35000"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div class="p-3 bg-gray-50 rounded-xl text-xs space-y-1 text-gray-600">
                    <div class="flex justify-between">
                        <span>Ongkos Kirim Driver:</span>
                        <span class="font-bold text-green-700">Rp {{ number_format($activeOrder->shipping_fee ?? 10000, 0, ',', '.') }}</span>
                    </div>
                    <p class="text-[10px] text-gray-400 pt-1 border-t border-gray-200">
                        Total tagihan ke customer = (Belanjaan Riil + Ongkir).
                    </p>
                </div>
                <div class="flex items-center gap-2 pt-2">
                    <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 rounded-lg text-xs shadow-sm transition">
                        Konfirmasi & Selesai
                    </button>
                    <button type="button" onclick="document.getElementById('completeCustomModal').classList.add('hidden')"
                        class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-semibold rounded-lg transition">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Live Chat Pesanan Aktif untuk Driver --}}
    <div class="mt-6 pt-5 border-t border-gray-100">
        <h3 class="font-bold text-gray-800 text-sm mb-3">💬 Hubungi Pelanggan (Live Chat)</h3>
        <x-order-chat :order="$activeOrder" :readOnly="false" />
    </div>
</div>
@endif

{{-- Available Orders --}}
<div class="mb-4 flex items-center justify-between">
    <h2 class="text-lg font-bold text-gray-800">📋 Pesanan Masuk (Tersedia)</h2>
    @if($driver->driver_status === 'online')
    <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-semibold animate-pulse">Live</span>
    @endif
</div>

@if($driver->driver_status === 'resting')
<div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-xl px-5 py-4 text-sm mb-6 flex items-center gap-3">
    <span class="text-2xl">🟡</span>
    <div>
        <p class="font-bold">Status Anda: Istirahat</p>
        <p class="text-xs text-yellow-700">Ubah status ke <strong>Aktif</strong> jika sudah siap kembali menerima dan mengambil pesanan.</p>
    </div>
</div>
@elseif($driver->driver_status === 'offline')
<div class="bg-gray-100 border border-gray-200 text-gray-700 rounded-xl px-5 py-4 text-sm mb-6 flex items-center gap-3">
    <span class="text-2xl">⚫</span>
    <div>
        <p class="font-bold">Status Anda: Offline</p>
        <p class="text-xs text-gray-500">Klik tombol <strong>🟢 Aktif</strong> di atas untuk mulai melihat dan mengambil pesanan masuk.</p>
    </div>
</div>
@endif

<div class="space-y-4">
    @forelse($availableOrders as $order)
    <div class="bg-white rounded-xl shadow-sm border {{ $order->is_custom ? 'border-purple-200 bg-purple-50/20' : 'border-gray-100' }} p-5 flex items-center justify-between gap-4">
        <div class="flex-1">
            <div class="flex items-center gap-2 mb-1">
                <span class="font-bold text-sm text-gray-800 font-mono">#{{ $order->id }}</span>
                @if($order->is_custom)
                    <span class="text-[10px] bg-purple-100 text-purple-700 font-bold px-2 py-0.5 rounded-full">🛍️ Toko Non-Mitra</span>
                @endif
                <span class="text-xs text-gray-400">{{ $order->created_at->diffForHumans() }}</span>
            </div>
            <p class="text-sm font-semibold text-gray-800">{{ $order->display_store_name }}</p>
            @if($order->display_store_address)
                <p class="text-xs text-gray-500 mt-0.5">🏪 Lokasi Toko: {{ Str::limit($order->display_store_address, 70) }}</p>
            @endif
            <p class="text-xs text-gray-500 mt-0.5">📍 Antar ke: {{ Str::limit($order->delivery_address, 70) }}</p>
            <p class="text-xs text-gray-400 mt-1">👤 Pelanggan: {{ $order->customer->name ?? '-' }} (📞 {{ $order->customer->phone ?? '-' }})</p>
            @if($order->is_custom && $order->notes)
                <p class="text-xs text-purple-800 mt-1.5 italic bg-white/80 p-2 rounded border border-purple-100">
                    "{{ Str::limit($order->notes, 90) }}"
                </p>
            @endif
        </div>
        <div class="text-right shrink-0">
            @if($order->is_custom)
                <span class="text-[11px] text-gray-400 block">Ongkir Kurir:</span>
                <p class="font-extrabold text-green-600 text-sm">Rp {{ number_format($order->shipping_fee ?? 10000, 0, ',', '.') }}</p>
                <span class="text-[10px] text-purple-600 block mt-0.5 font-medium">+ Belanjaan Riil</span>
            @else
                <p class="font-extrabold text-orange-600 text-sm">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</p>
            @endif
            <form method="POST" action="{{ route('driver.take-order', $order) }}" class="mt-2">
                @csrf
                <button type="submit"
                    class="bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold px-4 py-2 rounded-lg transition shadow-sm">
                    Ambil Pesanan
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="text-center py-12 bg-white rounded-xl border border-gray-100 text-gray-400">
        <div class="text-4xl mb-2">📭</div>
        <p class="text-sm font-medium">Tidak ada pesanan tersedia saat ini.</p>
    </div>
    @endforelse
</div>

@endsection
