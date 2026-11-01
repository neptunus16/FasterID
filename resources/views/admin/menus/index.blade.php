@extends('layouts.admin')
@section('title', 'Menu ' . $store->store_name)
@section('page-title', 'Detail Toko & Manajemen Menu')
@section('content')

{{-- Header Info Toko --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
    <div class="flex items-center gap-4">
        @if($store->image)
            <img src="{{ asset('storage/' . $store->image) }}" alt="{{ $store->store_name }}" class="w-16 h-16 rounded-xl object-cover border border-gray-100 shadow-sm">
        @else
            <div class="w-16 h-16 rounded-xl bg-orange-50 border border-orange-100 flex items-center justify-center text-3xl">🏪</div>
        @endif
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-bold text-gray-800">{{ $store->store_name }}</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $store->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                    {{ $store->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-1">📍 {{ $store->address }} · 📞 {{ $store->phone ?? 'Tidak ada telepon' }}</p>
        </div>
    </div>
    <div class="flex items-center gap-2 self-stretch md:self-auto">
        <a href="{{ route('admin.stores') }}" class="px-3.5 py-2 rounded-lg border border-gray-200 text-xs font-semibold text-gray-600 hover:bg-gray-50 transition">
            ← Kembali ke Toko
        </a>
        <a href="{{ route('admin.stores.menus.create', $store) }}" class="bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold px-4 py-2 rounded-lg shadow-sm transition flex items-center gap-1.5">
            <span>+</span> Tambah Menu Baru
        </a>
    </div>
</div>

{{-- Daftar Menu Toko --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h2 class="font-bold text-gray-800 text-sm">Daftar Menu Makanan & Minuman</h2>
            <p class="text-xs text-gray-400">Total: <strong>{{ $menus->total() }}</strong> menu tersedia di toko ini</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 text-left">Foto & Nama Menu</th>
                    <th class="px-6 py-3 text-left">Deskripsi</th>
                    <th class="px-6 py-3 text-left">Harga</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($menus as $menu)
                <tr class="hover:bg-gray-50/80 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if($menu->image)
                                <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-12 h-12 rounded-lg object-cover border border-gray-100">
                            @else
                                <div class="w-12 h-12 rounded-lg bg-orange-50 text-orange-400 flex items-center justify-center text-xl">🍱</div>
                            @endif
                            <div>
                                <p class="font-semibold text-gray-800">{{ $menu->name }}</p>
                                <span class="text-[11px] text-gray-400">ID: #{{ $menu->id }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-500 max-w-xs">
                        {{ $menu->description ?: '-' }}
                    </td>
                    <td class="px-6 py-4 font-bold text-orange-600">
                        Rp {{ number_format($menu->price, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $menu->is_available ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                            {{ $menu->is_available ? 'Tersedia' : 'Habis' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.stores.menus.edit', [$store, $menu]) }}" class="bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.stores.menus.destroy', [$store, $menu]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded-lg text-xs font-semibold transition" onclick="return confirm('Apakah Anda yakin ingin menghapus menu {{ $menu->name }}?')">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                        <div class="text-4xl mb-2">🍽️</div>
                        <p class="text-sm font-medium">Belum ada menu untuk toko ini.</p>
                        <a href="{{ route('admin.stores.menus.create', $store) }}" class="text-orange-500 hover:underline text-xs mt-1 inline-block font-semibold">+ Tambah Menu Pertama</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($menus->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $menus->links() }}</div>
    @endif
</div>

@endsection

