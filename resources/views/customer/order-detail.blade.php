@extends('layouts.app')
@section('title', 'Detail Pesanan #' . $order->id)
@section('content')

<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-800">Detail Pesanan <span class="text-orange-500 font-mono">#{{ $order->id }}</span></h1>
    <a href="{{ route('customer.orders') }}" class="text-sm text-orange-500 hover:underline">← Kembali ke Riwayat</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Order Info --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Status Card --}}
        @php
            $statusMap = [
                'pending'    => ['label' => 'Menunggu Konfirmasi', 'class' => 'bg-yellow-50 border-yellow-300 text-yellow-700', 'icon' => '⏳'],
                'processing' => ['label' => 'Sedang Diproses Toko/Driver', 'class' => 'bg-blue-50 border-blue-300 text-blue-700', 'icon' => '👨‍🍳'],
                'delivering' => ['label' => 'Sedang Diantar Driver',      'class' => 'bg-orange-50 border-orange-300 text-orange-700', 'icon' => '🏍️'],
                'completed'  => ['label' => 'Pesanan Selesai',     'class' => 'bg-green-50 border-green-300 text-green-700',    'icon' => '✅'],
                'cancelled'  => ['label' => 'Pesanan Dibatalkan',  'class' => 'bg-red-50 border-red-300 text-red-600',          'icon' => '❌'],
            ];
            $s = $statusMap[$order->status] ?? ['label' => $order->status, 'class' => 'bg-gray-50 border-gray-300 text-gray-600', 'icon' => '📦'];
        @endphp
        <div class="border rounded-xl px-5 py-4 {{ $s['class'] }} flex items-center gap-3">
            <span class="text-3xl">{{ $s['icon'] }}</span>
            <div>
                <p class="font-bold text-lg">{{ $s['label'] }}</p>
                <p class="text-xs opacity-75">Dipesan pada {{ $order->created_at->format('d M Y, H:i') }} WIB</p>
            </div>
        </div>

        {{-- Items & Bill Breakdown --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 font-semibold text-gray-700 text-sm flex items-center justify-between">
                <span>🧾 Rincian Pesanan</span>
                @if($order->is_custom)
                    <span class="text-xs bg-purple-100 text-purple-700 font-bold px-2 py-0.5 rounded-full">🛍️ Toko Non-Mitra</span>
                @endif
            </div>

            @if($order->is_custom)
                {{-- Tampilan Rincian Pesanan Toko Non-Mitra --}}
                <div class="p-5 space-y-3">
                    <div>
                        <span class="text-xs text-gray-400 block font-semibold">Toko Tujuan:</span>
                        <p class="font-bold text-gray-800">{{ $order->store_name }}</p>
                        @if($order->store_address)
                            <p class="text-xs text-gray-500 mt-0.5">📍 {{ $order->store_address }}</p>
                        @endif
                    </div>
                    <div class="pt-2 border-t border-gray-100">
                        <span class="text-xs text-gray-400 block font-semibold mb-1">Catatan Pesanan / Menu Yang Dipesan:</span>
                        <div class="p-3 bg-orange-50/60 rounded-xl text-xs font-mono text-gray-800 whitespace-pre-line border border-orange-100">
                            {{ $order->notes ?: '-' }}
                        </div>
                    </div>
                </div>
            @else
                {{-- Tampilan Rincian Item Mitra Reguler --}}
                <div class="divide-y divide-gray-50">
                    @foreach($order->items as $item)
                    <div class="px-5 py-3 flex justify-between text-sm">
                        <div>
                            <p class="font-medium text-gray-800">{{ $item->name }}</p>
                            <p class="text-gray-400 text-xs">{{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                        </div>
                        <p class="font-semibold text-gray-700">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                    </div>
                    @endforeach
                </div>
            @endif

            {{-- Breakdown Harga --}}
            <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50 space-y-1.5 text-sm">
                @if($order->is_custom)
                    <div class="flex justify-between text-gray-600">
                        <span>Total Belanja Toko (Riil):</span>
                        <span class="font-semibold text-gray-800">
                            @if($order->shopping_cost > 0)
                                Rp {{ number_format($order->shopping_cost, 0, ',', '.') }}
                            @else
                                <span class="text-xs text-orange-600 italic">Dikonfirmasi Driver di Toko</span>
                            @endif
                        </span>
                    </div>
                @else
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal Produk:</span>
                        <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-gray-600">
                    <span>Ongkos Kirim Kurir:</span>
                    <span>Rp {{ number_format($order->shipping_fee ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-bold text-base text-gray-900 pt-2 border-t border-gray-200">
                    <span>Total Pembayaran:</span>
                    <span class="text-orange-600">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar Info --}}
    <div class="space-y-5">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-sm space-y-3">
            <h3 class="font-semibold text-gray-700 mb-2">📋 Info Pengiriman</h3>
            <div>
                <p class="text-gray-400 text-xs">Lokasi Toko</p>
                <p class="font-medium text-gray-800">{{ $order->display_store_name }}</p>
                @if($order->display_store_address)
                    <p class="text-xs text-gray-500 mt-0.5">📍 {{ $order->display_store_address }}</p>
                @endif
            </div>
            <div><p class="text-gray-400 text-xs">Alamat Tujuan</p><p class="font-medium text-gray-800">{{ $order->delivery_address }}</p></div>
            <div>
                <p class="text-gray-400 text-xs">Metode Pembayaran</p>
                <p class="font-medium text-gray-800 capitalize">
                    {{ $order->payment_method === 'cash' ? '💵 Tunai (COD)' : '💳 Transfer Bank' }}
                </p>
            </div>
            @if($order->customer_note)
            <div><p class="text-gray-400 text-xs">Catatan Pembeli</p><p class="font-medium text-gray-800 italic">"{{ $order->customer_note }}"</p></div>
            @endif
        </div>

        @if($order->driver)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-sm space-y-2">
            <h3 class="font-semibold text-gray-700 mb-2">🏍️ Driver Pengantar</h3>
            <p class="font-semibold text-gray-800">{{ $order->driver->name }}</p>
            <p class="text-gray-500 text-xs">📞 {{ $order->driver->phone ?? '-' }}</p>
        </div>
        @endif

        {{-- Widget Live Chat Jika Order Sudah Diproses/Diantar Driver --}}
        @if(in_array($order->status, ['processing', 'delivering', 'completed']) && $order->driver)
        <div>
            <x-order-chat :order="$order" :readOnly="false" />
        </div>
        @endif
    </div>
</div>

@endsection
