@extends('layouts.app')
@section('title', 'Riwayat Pengiriman')
@section('content')

<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-800">📜 Riwayat Pengiriman</h1>
    <a href="{{ route('driver.dashboard') }}" class="text-sm text-orange-500 hover:underline">← Kembali</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-6 py-3 text-left">#</th>
                <th class="px-6 py-3 text-left">Toko</th>
                <th class="px-6 py-3 text-left">Pelanggan</th>
                <th class="px-6 py-3 text-left">Ongkir</th>
                <th class="px-6 py-3 text-left">Selesai</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($orders as $order)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-gray-500 font-mono font-bold">#{{ $order->id }}</td>
                <td class="px-6 py-4 font-medium text-gray-800">
                    {{ $order->display_store_name }}
                    @if($order->is_custom)
                        <span class="ml-1 text-[10px] bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded font-bold">Non-Mitra</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-gray-500">{{ $order->customer->name ?? '-' }}</td>
                <td class="px-6 py-4 font-bold text-green-600">Rp {{ number_format($order->shipping_fee ?? 0, 0, ',', '.') }}</td>
                <td class="px-6 py-4 text-gray-400">{{ $order->updated_at->format('d M Y H:i') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-gray-400">Belum ada riwayat pengiriman.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($orders->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $orders->links() }}</div>
    @endif
</div>

@endsection

