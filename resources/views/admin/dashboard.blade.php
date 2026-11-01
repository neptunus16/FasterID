@extends('layouts.admin')
@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')
@section('content')

{{-- Stat Cards --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
    @foreach([
        ['bg-blue-500',   '👥', 'Customer',         $stats['total_customers']],
        ['bg-green-500',  '🏍️', 'Driver Terdaftar', $stats['total_drivers']],
        ['bg-yellow-500', '⏳', 'Driver Pending',    $stats['pending_drivers']],
        ['bg-orange-500', '🏪', 'Toko Mitra',       $stats['total_stores']],
        ['bg-purple-500', '📦', 'Total Pesanan',     $stats['total_orders']],
        ['bg-red-500',    '📅', 'Pesanan Hari Ini',  $stats['orders_today']],
    ] as [$color, $icon, $label, $val])
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 {{ $color }} border-opacity-60" style="border-left-color: inherit;">
        <div class="text-2xl mb-1">{{ $icon }}</div>
        <div class="text-2xl font-bold text-gray-800">{{ $val }}</div>
        <div class="text-xs text-gray-500 mt-0.5">{{ $label }}</div>
    </div>
    @endforeach
</div>

{{-- Driver Real-time Monitoring Section --}}
<div class="mb-8">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <span>📡</span> Pemantauan Driver Real-Time
            </h2>
            <p class="text-xs text-gray-500">Pantau status kesiapan armada pengantaran (Aktif, Istirahat, Offline)</p>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-50 text-green-700 border border-green-200 font-semibold">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span> {{ $driverMonitor['online'] }} Aktif
            </span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-yellow-50 text-yellow-700 border border-yellow-200 font-semibold">
                <span class="w-2 h-2 rounded-full bg-yellow-500"></span> {{ $driverMonitor['resting'] }} Istirahat
            </span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 border border-gray-200 font-semibold">
                <span class="w-2 h-2 rounded-full bg-gray-400"></span> {{ $driverMonitor['offline'] }} Offline
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Driver Aktif (Online) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col">
            <div class="px-5 py-3.5 border-b border-gray-100 bg-green-50/50 rounded-t-xl flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-base">🟢</span>
                    <span class="font-bold text-sm text-green-800">Driver Aktif (Siap Order)</span>
                </div>
                <span class="text-xs font-bold bg-green-200 text-green-800 px-2 py-0.5 rounded-full">{{ $activeDrivers->count() }}</span>
            </div>
            <div class="p-4 flex-1 divide-y divide-gray-50 max-h-72 overflow-y-auto">
                @forelse($activeDrivers as $d)
                <div class="py-2.5 first:pt-0 last:pb-0 flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-sm text-gray-800">{{ $d->name }}</p>
                        <p class="text-xs text-gray-500">📞 {{ $d->phone ?? '-' }}</p>
                    </div>
                    <span class="text-[11px] px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-medium">Online</span>
                </div>
                @empty
                <div class="py-8 text-center text-gray-400 text-xs">
                    <p>Tidak ada driver yang aktif saat ini.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Driver Istirahat (Resting) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col">
            <div class="px-5 py-3.5 border-b border-gray-100 bg-yellow-50/50 rounded-t-xl flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-base">🟡</span>
                    <span class="font-bold text-sm text-yellow-800">Driver Istirahat</span>
                </div>
                <span class="text-xs font-bold bg-yellow-200 text-yellow-800 px-2 py-0.5 rounded-full">{{ $restingDrivers->count() }}</span>
            </div>
            <div class="p-4 flex-1 divide-y divide-gray-50 max-h-72 overflow-y-auto">
                @forelse($restingDrivers as $d)
                <div class="py-2.5 first:pt-0 last:pb-0 flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-sm text-gray-800">{{ $d->name }}</p>
                        <p class="text-xs text-gray-500">📞 {{ $d->phone ?? '-' }}</p>
                    </div>
                    <span class="text-[11px] px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700 font-medium">Istirahat</span>
                </div>
                @empty
                <div class="py-8 text-center text-gray-400 text-xs">
                    <p>Tidak ada driver yang sedang istirahat.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Driver Offline --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col">
            <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50 rounded-t-xl flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-base">⚫</span>
                    <span class="font-bold text-sm text-gray-700">Driver Offline</span>
                </div>
                <span class="text-xs font-bold bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full">{{ $offlineDrivers->count() }}</span>
            </div>
            <div class="p-4 flex-1 divide-y divide-gray-50 max-h-72 overflow-y-auto">
                @forelse($offlineDrivers as $d)
                <div class="py-2.5 first:pt-0 last:pb-0 flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-sm text-gray-800">{{ $d->name }}</p>
                        <p class="text-xs text-gray-500">📞 {{ $d->phone ?? '-' }}</p>
                    </div>
                    <span class="text-[11px] px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 font-medium">Offline</span>
                </div>
                @empty
                <div class="py-8 text-center text-gray-400 text-xs">
                    <p>Semua driver sedang bertugas / istirahat.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Pending Drivers Approval Table --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-8">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h2 class="font-semibold text-gray-800">⏳ Pendaftaran Driver Menunggu Persetujuan</h2>
            <p class="text-xs text-gray-400">Verifikasi driver baru sebelum mereka dapat mulai menerima pesanan</p>
        </div>
        <span class="bg-yellow-100 text-yellow-700 text-xs font-semibold px-2.5 py-1 rounded-full">{{ $pendingDrivers->count() }} pending</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 text-left">Nama</th>
                    <th class="px-6 py-3 text-left">Email</th>
                    <th class="px-6 py-3 text-left">Telepon</th>
                    <th class="px-6 py-3 text-left">Alamat</th>
                    <th class="px-6 py-3 text-left">Waktu Daftar</th>
                    <th class="px-6 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($pendingDrivers as $driver)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-800">{{ $driver->name }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ $driver->email }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ $driver->phone ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-500 max-w-xs truncate">{{ $driver->address ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-400">{{ $driver->created_at->diffForHumans() }}</td>
                    <td class="px-6 py-4 flex gap-2">
                        <form method="POST" action="{{ route('admin.driver.approve', $driver) }}">
                            @csrf
                            <button class="bg-green-500 hover:bg-green-600 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition">✅ Setujui</button>
                        </form>
                        <form method="POST" action="{{ route('admin.driver.reject', $driver) }}">
                            @csrf
                            <button class="bg-red-500 hover:bg-red-600 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition" onclick="return confirm('Tolak pendaftaran driver ini?')">❌ Tolak</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-gray-400">Tidak ada pendaftaran driver yang menunggu persetujuan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Section: Pesanan Terbaru & Menunggu Konfirmasi --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <h2 class="font-bold text-gray-800 text-base flex items-center gap-2">
                <span>📦</span> Pesanan Masuk & Terbaru
            </h2>
            @if($pendingOrdersCount > 0)
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-700 animate-pulse border border-orange-200">
                🔔 {{ $pendingOrdersCount }} Butuh Tindakan
            </span>
            @endif
        </div>
        <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-orange-600 hover:text-orange-700 hover:underline">
            Lihat Semua Pesanan →
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 text-left"># Order</th>
                    <th class="px-6 py-3 text-left">Pelanggan</th>
                    <th class="px-6 py-3 text-left">Toko Mitra</th>
                    <th class="px-6 py-3 text-left">Total Bayar</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Waktu Masuk</th>
                    <th class="px-6 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($recentOrders as $order)
                @php
                    $badge = $order->statusBadge();
                    $finalTotal = $order->total_price ?: ($order->total_amount + ($order->shipping_fee ?? 0));
                @endphp
                <tr class="hover:bg-gray-50/80 transition {{ $order->status === 'pending' ? 'bg-orange-50/30' : '' }}">
                    <td class="px-6 py-4 font-mono font-bold text-gray-800">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-orange-600 hover:underline">
                            #{{ $order->id }}
                        </a>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-semibold text-gray-800">{{ $order->customer->name ?? '-' }}</p>
                        <p class="text-xs text-gray-400">📞 {{ $order->customer->phone ?? '-' }}</p>
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-700">
                        {{ $order->store->store_name ?? '-' }}
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-extrabold text-orange-600">Rp {{ number_format($finalTotal, 0, ',', '.') }}</p>
                        <span class="text-[10px] text-gray-400 font-medium">Ongkir: Rp {{ number_format($order->shipping_fee ?? 0, 0, ',', '.') }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badge['color'] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                            {{ $badge['label'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-400">
                        {{ $order->created_at->diffForHumans() }}
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.orders.show', $order) }}" class="px-3 py-1.5 rounded-lg bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold transition inline-flex items-center gap-1 shadow-sm">
                            Kelola Order
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                        <div class="text-4xl mb-2">📭</div>
                        <p class="text-sm font-medium">Belum ada pesanan yang masuk.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
