@extends('layouts.app')
@section('title', 'Riwayat Pesanan')
@section('content')

<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-800">📦 Riwayat Pesanan Saya</h1>
    <a href="{{ route('customer.dashboard') }}" class="text-sm text-orange-500 hover:underline">← Kembali ke Beranda</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-6 py-3 text-left"># Order</th>
                <th class="px-6 py-3 text-left">Toko</th>
                <th class="px-6 py-3 text-left">Total Bayar</th>
                <th class="px-6 py-3 text-left">Status</th>
                <th class="px-6 py-3 text-left">Waktu</th>
                <th class="px-6 py-3 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($orders as $order)
            @php
                $statusMap = [
                    'pending'    => ['label' => 'Menunggu',    'class' => 'bg-yellow-100 text-yellow-700'],
                    'processing' => ['label' => 'Diproses',    'class' => 'bg-blue-100 text-blue-700'],
                    'delivering' => ['label' => 'Diantar',     'class' => 'bg-orange-100 text-orange-700'],
                    'completed'  => ['label' => 'Selesai',     'class' => 'bg-green-100 text-green-700'],
                    'cancelled'  => ['label' => 'Dibatalkan',  'class' => 'bg-red-100 text-red-600'],
                ];
                $s = $statusMap[$order->status] ?? ['label' => $order->status, 'class' => 'bg-gray-100 text-gray-600'];
                $finalTotal = $order->total_price ?: ($order->total_amount + ($order->shipping_fee ?? 0));
            @endphp
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-gray-800 font-mono font-bold">#{{ $order->id }}</td>
                <td class="px-6 py-4 font-medium text-gray-800">
                    {{ $order->display_store_name }}
                    @if($order->is_custom)
                        <span class="ml-1 text-[10px] bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded font-bold">Non-Mitra</span>
                    @endif
                </td>
                <td class="px-6 py-4 font-bold text-orange-600">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                <td class="px-6 py-4">
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $s['class'] }}">{{ $s['label'] }}</span>
                </td>
                <td class="px-6 py-4 text-gray-400">{{ $order->created_at->diffForHumans() }}</td>
                <td class="px-6 py-4">
                    <a href="{{ route('customer.order.detail', $order) }}" class="inline-flex items-center gap-1 text-orange-500 hover:text-orange-600 text-xs font-semibold">
                        Lihat Detail →
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-16 text-center text-gray-400">
                    <div class="text-5xl mb-3">📭</div>
                    <p class="font-medium text-gray-600">Belum ada riwayat pesanan.</p>
                    <a href="{{ route('customer.dashboard') }}" class="text-orange-500 hover:underline text-sm mt-2 inline-block font-semibold">Mulai pesan makanan favoritmu sekarang</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($orders->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $orders->links() }}</div>
    @endif
</div>

@endsection
