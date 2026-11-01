@extends('layouts.admin')
@section('title', 'Kelola Pesanan')
@section('page-title', 'Kelola & Riwayat Seluruh Pesanan')
@section('content')

{{-- Filter Tabs Status --}}
<div class="flex items-center gap-2 overflow-x-auto pb-4 mb-6 text-xs font-semibold">
    <a href="{{ route('admin.orders.index') }}"
        class="px-4 py-2 rounded-xl transition whitespace-nowrap {{ !$status ? 'bg-orange-500 text-white shadow-sm' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }}">
        Semua Pesanan ({{ $counts['all'] }})
    </a>
    <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}"
        class="px-4 py-2 rounded-xl transition whitespace-nowrap {{ $status === 'pending' ? 'bg-yellow-500 text-white shadow-sm' : 'bg-white text-yellow-700 hover:bg-yellow-50 border border-yellow-200' }}">
        ⏳ Menunggu ({{ $counts['pending'] }})
    </a>
    <a href="{{ route('admin.orders.index', ['status' => 'processing']) }}"
        class="px-4 py-2 rounded-xl transition whitespace-nowrap {{ $status === 'processing' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-blue-700 hover:bg-blue-50 border border-blue-200' }}">
        👨‍🍳 Diproses ({{ $counts['processing'] }})
    </a>
    <a href="{{ route('admin.orders.index', ['status' => 'delivering']) }}"
        class="px-4 py-2 rounded-xl transition whitespace-nowrap {{ $status === 'delivering' ? 'bg-orange-600 text-white shadow-sm' : 'bg-white text-orange-700 hover:bg-orange-50 border border-orange-200' }}">
        🏍️ Sedang Diantar ({{ $counts['delivering'] }})
    </a>
    <a href="{{ route('admin.orders.index', ['status' => 'completed']) }}"
        class="px-4 py-2 rounded-xl transition whitespace-nowrap {{ $status === 'completed' ? 'bg-green-600 text-white shadow-sm' : 'bg-white text-green-700 hover:bg-green-50 border border-green-200' }}">
        ✅ Selesai ({{ $counts['completed'] }})
    </a>
    <a href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}"
        class="px-4 py-2 rounded-xl transition whitespace-nowrap {{ $status === 'cancelled' ? 'bg-red-600 text-white shadow-sm' : 'bg-white text-red-700 hover:bg-red-50 border border-red-200' }}">
        ❌ Dibatalkan ({{ $counts['cancelled'] }})
    </a>
</div>

{{-- Tabel Daftar Pesanan --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h2 class="font-bold text-gray-800 text-sm">Daftar Transaksi Pesanan</h2>
            <p class="text-xs text-gray-400">Total data: <strong>{{ $orders->total() }}</strong> pesanan</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 text-left"># Order</th>
                    <th class="px-6 py-3 text-left">Pelanggan</th>
                    <th class="px-6 py-3 text-left">Toko</th>
                    <th class="px-6 py-3 text-left">Rincian Pembayaran</th>
                    <th class="px-6 py-3 text-left">Driver</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Waktu</th>
                    <th class="px-6 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($orders as $order)
                @php
                    $badge = $order->statusBadge();
                    $finalTotal = $order->total_price ?: ($order->total_amount + ($order->shipping_fee ?? 0));
                @endphp
                <tr class="hover:bg-gray-50/80 transition">
                    <td class="px-6 py-4 font-mono font-bold text-gray-800">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-orange-600 hover:underline">
                            #{{ $order->id }}
                        </a>
                        @if($order->is_custom)
                            <span class="block text-[10px] text-purple-700 bg-purple-100 font-bold px-1.5 py-0.5 rounded w-fit mt-0.5">Non-Mitra</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-semibold text-gray-800">{{ $order->customer->name ?? 'User Terhapus' }}</p>
                        <p class="text-xs text-gray-400">{{ $order->customer->phone ?? '-' }}</p>
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-700">
                        {{ $order->display_store_name }}
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-extrabold text-orange-600">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</p>
                        <p class="text-[11px] text-gray-400">
                            @if($order->is_custom)
                                Belanja: Rp {{ number_format($order->shopping_cost ?? 0, 0, ',', '.') }} | Ongkir: Rp {{ number_format($order->shipping_fee ?? 10000, 0, ',', '.') }}
                            @else
                                Menu: Rp {{ number_format($order->total_amount, 0, ',', '.') }} | Ongkir: Rp {{ number_format($order->shipping_fee ?? 0, 0, ',', '.') }}
                            @endif
                        </p>
                    </td>
                    <td class="px-6 py-4 text-xs">
                        @if($order->driver)
                            <span class="font-semibold text-gray-800">🏍️ {{ $order->driver->name }}</span>
                        @else
                            <span class="text-gray-400 italic">Belum ada</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badge['color'] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                            {{ $badge['label'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-400">
                        {{ $order->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.orders.show', $order) }}" class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold transition inline-block">
                            Detail & Kelola
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                        <div class="text-4xl mb-2">📦</div>
                        <p class="text-sm font-medium">Tidak ada data pesanan yang sesuai dengan filter.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $orders->links() }}</div>
    @endif
</div>

@endsection

