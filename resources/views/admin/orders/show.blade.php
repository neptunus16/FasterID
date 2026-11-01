@extends('layouts.admin')
@section('title', 'Detail Pesanan #' . $order->id)
@section('page-title', 'Detail & Kelola Status Pesanan #' . $order->id)
@section('content')

<div class="mb-6">
    <a href="{{ route('admin.orders.index') }}" class="text-xs text-orange-600 hover:underline flex items-center gap-1 font-semibold">
        ← Kembali ke Daftar Pesanan
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Kolom Kiri: Rincian Pesanan & Item --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Kartu Status Saat Ini --}}
        @php
            $badge = $order->statusBadge();
            $finalTotal = $order->grand_total;
        @endphp
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <span class="text-xs text-gray-400 uppercase font-semibold">Status Pesanan Saat Ini:</span>
                <div class="mt-1 flex items-center gap-2">
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-bold border {{ $badge['color'] }}">
                        <span class="w-2 h-2 rounded-full {{ $badge['dot'] }}"></span>
                        {{ $badge['label'] }}
                    </span>
                    <span class="text-xs text-gray-400">Dibuat: {{ $order->created_at->format('d M Y, H:i') }} WIB</span>
                </div>
            </div>
            <div class="text-left md:text-right">
                <span class="text-xs text-gray-400 uppercase font-semibold">Total Pembayaran:</span>
                <p class="text-2xl font-extrabold text-orange-600">Rp {{ number_format($finalTotal, 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Rincian Item Menu Yang Dipesan --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-800 text-sm">
                    🧾 Rincian Pesanan: {{ $order->display_store_name }}
                </h3>
                @if($order->is_custom)
                    <span class="text-xs bg-purple-100 text-purple-700 font-bold px-2.5 py-0.5 rounded-full">🛍️ Toko Non-Mitra</span>
                @endif
            </div>

            @if($order->is_custom)
                <div class="p-6 space-y-4">
                    <div>
                        <span class="text-xs text-gray-400 block font-semibold">Alamat / Lokasi Toko:</span>
                        <p class="text-sm font-medium text-gray-800">📍 {{ $order->store_address ?: '-' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400 block font-semibold mb-1">Daftar Barang / Makanan Dipesan Customer:</span>
                        <div class="p-4 bg-purple-50/70 rounded-xl text-xs font-mono text-purple-950 whitespace-pre-line border border-purple-100">
                            {{ $order->notes ?: '-' }}
                        </div>
                    </div>
                </div>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach($order->items as $item)
                    <div class="px-6 py-4 flex items-center justify-between text-sm">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $item->name }}</p>
                            <p class="text-xs text-gray-400">{{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                        </div>
                        <p class="font-bold text-gray-700">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                    </div>
                    @endforeach
                </div>
            @endif

            {{-- Breakdown Harga --}}
            <div class="bg-gray-50 p-6 border-t border-gray-100 space-y-2 text-sm">
                @if($order->is_custom)
                    <div class="flex justify-between text-gray-600">
                        <span>Total Belanja Toko Non-Mitra (Riil):</span>
                        <span class="font-semibold text-gray-800">
                            @if($order->shopping_cost > 0)
                                Rp {{ number_format($order->shopping_cost, 0, ',', '.') }}
                            @else
                                <span class="text-xs text-orange-600 italic">Belum diinput driver (Saat Selesai)</span>
                            @endif
                        </span>
                    </div>
                @else
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal Menu Produk:</span>
                        <span class="font-semibold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-gray-600">
                    <span>Ongkos Kirim Kurir:</span>
                    <span class="font-semibold">Rp {{ number_format($order->shipping_fee ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-base font-extrabold text-gray-900 pt-2 border-t border-gray-200">
                    <span>Total Keseluruhan (Grand Total):</span>
                    <span class="text-orange-600 font-extrabold">Rp {{ number_format($finalTotal, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Ubah Status & Detail Customer/Driver --}}
    <div class="space-y-6">

        {{-- Form Ubah Status Manual Admin --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 text-sm mb-4">⚙️ Kelola Status Pesanan</h3>
            <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Status Pesanan</label>
                    <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>⏳ Menunggu Konfirmasi</option>
                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>👨‍🍳 Sedang Diproses (Toko)</option>
                        <option value="delivering" {{ $order->status === 'delivering' ? 'selected' : '' }}>🏍️ Sedang Diantar Driver</option>
                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>✅ Pesanan Selesai</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>❌ Dibatalkan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Tetapkan / Ganti Driver</label>
                    <select name="driver_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <option value="">-- Belum Ada Driver --</option>
                        @foreach($availableDrivers as $driver)
                        <option value="{{ $driver->id }}" {{ $order->driver_id === $driver->id ? 'selected' : '' }}>
                            {{ $driver->name }} ({{ $driver->driverStatusLabel() }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-2.5 rounded-lg text-xs shadow-sm transition">
                    Simpan Perubahan
                </button>
            </form>
        </div>

        {{-- Info Customer & Alamat --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-3 text-sm">
            <h3 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-2">👤 Informasi Pembeli</h3>
            <div>
                <span class="text-xs text-gray-400 block">Nama Customer</span>
                <p class="font-semibold text-gray-800">{{ $order->customer->name ?? '-' }}</p>
            </div>
            <div>
                <span class="text-xs text-gray-400 block">Nomor Telepon</span>
                <p class="font-semibold text-gray-800">{{ $order->customer->phone ?? '-' }}</p>
            </div>
            <div>
                <span class="text-xs text-gray-400 block">Alamat Tujuan</span>
                <p class="font-medium text-gray-700">{{ $order->delivery_address }}</p>
            </div>
            <div>
                <span class="text-xs text-gray-400 block">Metode Pembayaran</span>
                <p class="font-semibold text-gray-800 capitalize">{{ $order->payment_method === 'cash' ? '💵 Tunai (COD)' : '💳 Transfer' }}</p>
            </div>
            @if($order->customer_note)
            <div>
                <span class="text-xs text-gray-400 block">Catatan Pesanan</span>
                <p class="italic text-gray-600 bg-gray-50 p-2 rounded text-xs">"{{ $order->customer_note }}"</p>
            </div>
            @endif
        </div>

        {{-- Widget Live Chat Pesanan (Mode Read-Only untuk Admin) --}}
        <div>
            <x-order-chat :order="$order" :readOnly="true" />
        </div>
    </div>
</div>

@endsection
