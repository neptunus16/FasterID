@extends('layouts.admin')
@section('title', 'Kelola Toko')
@section('page-title', 'Kelola Toko Mitra')
@section('content')

<div class="flex justify-between items-center mb-6">
    <p class="text-sm text-gray-500">Total: <strong>{{ $stores->total() }}</strong> toko</p>
    <a href="{{ route('admin.stores.create') }}" class="bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold px-4 py-2 rounded-lg">+ Tambah Toko</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-6 py-3 text-left">Nama Toko</th>
                <th class="px-6 py-3 text-left">Alamat</th>
                <th class="px-6 py-3 text-left">Telepon</th>
                <th class="px-6 py-3 text-left">Kelola Menu</th>
                <th class="px-6 py-3 text-left">Status</th>
                <th class="px-6 py-3 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($stores as $store)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-semibold text-gray-800">
                    <a href="{{ route('admin.stores.menus.index', $store) }}" class="hover:text-orange-600 flex items-center gap-2">
                        <span>🏪</span> {{ $store->store_name }}
                    </a>
                </td>
                <td class="px-6 py-4 text-gray-500 max-w-xs truncate">{{ $store->address }}</td>
                <td class="px-6 py-4 text-gray-500">{{ $store->phone ?? '-' }}</td>
                <td class="px-6 py-4">
                    <a href="{{ route('admin.stores.menus.index', $store) }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-orange-50 text-orange-600 hover:bg-orange-100 transition">
                        <span>🍔</span> {{ $store->menus_count }} Menu
                    </a>
                </td>
                <td class="px-6 py-4">
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $store->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                        {{ $store->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td class="px-6 py-4 flex gap-2">
                    <a href="{{ route('admin.stores.menus.index', $store) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium px-3 py-1.5 rounded-lg transition">Detail</a>
                    <a href="{{ route('admin.stores.edit', $store) }}" class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg transition">Edit</a>
                    <form method="POST" action="{{ route('admin.stores.destroy', $store) }}">
                        @csrf @method('DELETE')
                        <button class="bg-red-500 hover:bg-red-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg transition" onclick="return confirm('Hapus toko ini beserta menunya?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400">Belum ada toko mitra terdaftar.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if($stores->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $stores->links() }}</div>
    @endif
</div>

@endsection
