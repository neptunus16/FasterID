<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    /**
     * Tampilkan halaman detail toko beserta daftar menunya.
     */
    public function index(Store $store)
    {
        $menus = $store->menus()->latest()->paginate(10);
        return view('admin.menus.index', compact('store', 'menus'));
    }

    /**
     * Form tambah menu baru untuk toko tertentu.
     */
    public function create(Store $store)
    {
        return view('admin.menus.create', compact('store'));
    }

    /**
     * Simpan menu baru ke database (berelasi dengan store_id).
     */
    public function store(Request $request, Store $store)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:150'],
            'price'        => ['required', 'numeric', 'min:0'],
            'description'  => ['nullable', 'string', 'max:1000'],
            'image'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'is_available' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('menus', 'public');
        }

        $validated['is_available'] = $request->has('is_available');
        $validated['store_id']     = $store->id;

        $store->menus()->create($validated);

        return redirect()->route('admin.stores.menus.index', $store)
            ->with('success', "Menu '{$validated['name']}' berhasil ditambahkan ke toko {$store->store_name}.");
    }

    /**
     * Form edit menu yang sudah ada.
     */
    public function edit(Store $store, Menu $menu)
    {
        abort_if($menu->store_id !== $store->id, 404);
        return view('admin.menus.edit', compact('store', 'menu'));
    }

    /**
     * Update menu toko.
     */
    public function update(Request $request, Store $store, Menu $menu)
    {
        abort_if($menu->store_id !== $store->id, 404);

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:150'],
            'price'        => ['required', 'numeric', 'min:0'],
            'description'  => ['nullable', 'string', 'max:1000'],
            'image'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'is_available' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($menu->image && Storage::disk('public')->exists($menu->image)) {
                Storage::disk('public')->delete($menu->image);
            }
            $validated['image'] = $request->file('image')->store('menus', 'public');
        }

        $validated['is_available'] = $request->has('is_available');

        $menu->update($validated);

        return redirect()->route('admin.stores.menus.index', $store)
            ->with('success', "Menu '{$menu->name}' berhasil diperbarui.");
    }

    /**
     * Hapus menu dari toko.
     */
    public function destroy(Store $store, Menu $menu)
    {
        abort_if($menu->store_id !== $store->id, 404);

        if ($menu->image && Storage::disk('public')->exists($menu->image)) {
            Storage::disk('public')->delete($menu->image);
        }

        $menuName = $menu->name;
        $menu->delete();

        return redirect()->route('admin.stores.menus.index', $store)
            ->with('success', "Menu '{$menuName}' berhasil dihapus.");
    }
}

