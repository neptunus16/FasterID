@extends('layouts.admin')

@section('content')
<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Daftar Pelanggan (Customer)</h1>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    <th class="px-6 py-3">Nama Customer</th>
                    <th class="px-6 py-3">Email / Telepon</th>
                    <th class="px-6 py-3 text-center">Frekuensi Pesanan</th>
                    <th class="px-6 py-3 text-right">Total Transaksi Selesai</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-sm">
                @forelse($customers as $customer)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-900">
                        {{ $customer->name }}
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        <div>{{ $customer->email }}</div>
                        <div class="text-xs text-gray-400">{{ $customer->phone ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 text-center font-semibold text-orange-600">
                        {{ $customer->orders_count }} Pesanan
                    </td>
                    <td class="px-6 py-4 text-right font-bold text-green-600">
                        Rp {{ number_format($customer->orders_sum_total_price ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                        Belum ada data customer.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $customers->links() }}
    </div>
</div>
@endsection