@extends('layouts.app')
@section('title', 'Dashboard Customer')
@section('content')

<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">👋 Halo, {{ Auth::user()->name }}!</h1>
        <p class="text-gray-500 text-sm mt-1">Temukan makanan favoritmu hari ini atau pesan dari toko mana saja.</p>
    </div>
</div>

{{-- Banner Custom Order Promo --}}
<div class="bg-gradient-to-r from-orange-50 to-amber-50 border border-orange-200/80 rounded-2xl p-5 mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3.5">
        <div class="w-12 h-12 rounded-xl bg-orange-500 text-white flex items-center justify-center text-2xl shrink-0 shadow-sm">
            🛵
        </div>
        <div>
            <h3 class="font-bold text-gray-800 text-sm">Warung Langganan Anda Belum Masuk Daftar?</h3>
            <p class="text-xs text-gray-500 mt-0.5">Jangan khawatir! Driver kami bisa membelikan makanan dari warung/toko apa saja di kota Anda.</p>
        </div>
    </div>
    <a href="{{ route('customer.custom-order.create') }}" class="px-4 py-2 bg-white hover:bg-orange-50 text-orange-600 font-bold text-xs rounded-lg border border-orange-200 transition shrink-0">
        Mulai Buat Pesanan →
    </a>
</div>

{{-- Stores Grid --}}
<h2 class="text-lg font-semibold mb-4">🏪 Warung & Toko Mitra</h2>
<!-- Form Search Warung -->
<div class="mb-6">
    <form action="{{ route('customer.dashboard') }}" method="GET" class="flex gap-2">
        <input 
            type="text" 
            name="search" 
            value="{{ request('search') }}" 
            placeholder="Cari nama warung atau lokasi..." 
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
        >
        <button 
            type="submit" 
            class="px-6 py-2 bg-orange-500 text-white font-semibold rounded-lg hover:bg-orange-600 transition"
        >
            Cari
        </button>
        @if(request('search'))
            <a 
                href="{{ route('customer.dashboard') }}" 
                class="px-4 py-2 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 flex items-center justify-center"
            >
                Reset
            </a>
        @endif
    </form>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-10">
    @forelse($stores as $store)
    <a href="{{ route('customer.store', $store) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition overflow-hidden">
        @if($store->image)
            <img src="{{ asset('storage/' . $store->image) }}" alt="{{ $store->name }}" class="w-full h-36 object-cover">
        @else
            <div class="w-full h-36 bg-orange-50 flex items-center justify-center text-5xl">🏪</div>
        @endif
        <div class="p-4">
            <h3 class="font-semibold text-gray-800">{{ $store->store_name }}</h3>
            <p class="text-xs text-gray-400 mt-1 truncate">{{ $store->address }}</p>
            <span class="mt-2 inline-block px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700 font-medium">Buka</span>
        </div>
    </a>
    @empty
    <div class="col-span-3 text-center py-16 text-gray-400">
        <div class="text-5xl mb-3">🏪</div>
        <p>Belum ada toko mitra tersedia.</p>
    </div>
    @endforelse
</div>

{{-- Recent Orders --}}
@if($recentOrders->count())
<h2 class="text-lg font-semibold mb-4">📦 Pesanan Terbaru</h2>
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-6 py-3 text-left">#</th>
                <th class="px-6 py-3 text-left">Toko</th>
                <th class="px-6 py-3 text-left">Total</th>
                <th class="px-6 py-3 text-left">Status</th>
                <th class="px-6 py-3 text-left">Waktu</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($recentOrders as $order)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-3 text-gray-400 font-mono">#{{ $order->id }}</td>
                <td class="px-6 py-3 font-medium text-gray-800">
                    {{ $order->display_store_name }}
                    @if($order->is_custom)
                        <span class="ml-1 text-[10px] bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded font-bold">Non-Mitra</span>
                    @endif
                </td>
                <td class="px-6 py-3 font-bold text-orange-600">
                    Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                </td>
                <td class="px-6 py-3">
                    @php
                        $badge = $order->statusBadge();
                    @endphp
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $badge['color'] }}">
                        {{ $badge['label'] }}
                    </span>
                </td>
                <td class="px-6 py-3 text-gray-400">{{ $order->created_at->diffForHumans() }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-6 py-3 border-t border-gray-100">
        <a href="{{ route('customer.orders') }}" class="text-sm text-orange-500 hover:underline font-medium">Lihat semua pesanan →</a>
    </div>
</div>
@endif

@endsection

