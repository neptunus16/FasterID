<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // ── Dashboard ─────────────────────────────────────────────────────────────

    public function dashboard()
    {
        // ── Statistik umum ────────────────────────────────────────────────────
        $stats = [
            'total_customers' => User::where('role', 'customer')->count(),
            'total_drivers'   => User::where('role', 'driver')->count(),
            'pending_drivers' => User::where('role', 'driver')->where('status', 'pending')->count(),
            'total_stores'    => Store::count(),
            'total_orders'    => Order::count(),
            'orders_today'    => Order::whereDate('created_at', today())->count(),
        ];

        // ── Driver menunggu persetujuan ────────────────────────────────────────
        $pendingDrivers = User::where('role', 'driver')
            ->where('status', 'pending')
            ->latest()
            ->get();

        // ── Monitoring driver aktif (status = active) berdasarkan driver_status
        $activeDrivers  = User::where('role', 'driver')->where('status', 'active')
            ->where('driver_status', 'online')
            ->latest('updated_at')
            ->get();

        $restingDrivers = User::where('role', 'driver')->where('status', 'active')
            ->where('driver_status', 'resting')
            ->latest('updated_at')
            ->get();

        $offlineDrivers = User::where('role', 'driver')->where('status', 'active')
            ->where('driver_status', 'offline')
            ->latest('updated_at')
            ->get();

        // ── Ringkasan monitoring ───────────────────────────────────────────────
        $driverMonitor = [
            'online'  => $activeDrivers->count(),
            'resting' => $restingDrivers->count(),
            'offline' => $offlineDrivers->count(),
        ];

        // ── Pesanan Terbaru & Menunggu Konfirmasi ─────────────────────────────
        $recentOrders = Order::with(['customer', 'store', 'driver'])
            ->latest()
            ->take(6)
            ->get();

        $pendingOrdersCount = Order::where('status', 'pending')->count();

        return view('admin.dashboard', compact(
            'stats',
            'pendingDrivers',
            'activeDrivers',
            'restingDrivers',
            'offlineDrivers',
            'driverMonitor',
            'recentOrders',
            'pendingOrdersCount'
        ));
    }

    // ── Driver Approval ───────────────────────────────────────────────────────

    public function approveDriver(User $driver)
    {
        abort_if($driver->role !== 'driver', 403);
        $driver->update(['status' => 'active']);
        return back()->with('success', "Driver {$driver->name} berhasil disetujui.");
    }

    public function rejectDriver(User $driver)
    {
        abort_if($driver->role !== 'driver', 403);
        $driver->update(['status' => 'rejected']);
        return back()->with('success', "Driver {$driver->name} telah ditolak.");
    }

    // ── Stores CRUD ───────────────────────────────────────────────────────────

    public function stores()
    {
        $stores = Store::withCount('menus')->latest()->paginate(15);
        return view('admin.stores', compact('stores'));
    }

    public function storeCreate()
    {
        return view('admin.stores-form', ['store' => null]);
    }

    public function storeStore(Request $request)
    {
        $data = $request->validate([
            'store_name' => ['required', 'string', 'max:150'],
            'address'    => ['required', 'string'],
            'phone'      => ['nullable', 'string', 'max:20'],
            'image'      => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('stores', 'public');
        }

        Store::create($data);
        return redirect()->route('admin.stores')->with('success', 'Toko berhasil ditambahkan.');
    }

    public function storeEdit(Store $store)
    {
        return view('admin.stores-form', compact('store'));
    }

    public function storeUpdate(Request $request, Store $store)
    {
        $data = $request->validate([
            'store_name' => ['required', 'string', 'max:150'],
            'address'    => ['required', 'string'],
            'phone'      => ['nullable', 'string', 'max:20'],
            'image'      => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('stores', 'public');
        }

        $data['is_active'] = $request->has('is_active');
        $store->update($data);
        return redirect()->route('admin.stores')->with('success', 'Toko berhasil diperbarui.');
    }

    public function storeDestroy(Store $store)
    {
        $store->delete();
        return back()->with('success', 'Toko berhasil dihapus.');
    }

    // ── Menus CRUD ────────────────────────────────────────────────────────────

    public function menus(Store $store)
    {
        $menus = $store->menus()->latest()->get();
        return view('admin.menus', compact('store', 'menus'));
    }

    public function menuStore(Request $request, Store $store)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'price'       => ['required', 'numeric', 'min:0'],
            'image'       => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menus', 'public');
        }

        $store->menus()->create($data);
        return back()->with('success', 'Menu berhasil ditambahkan.');
    }

    public function menuUpdate(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:150'],
            'description'  => ['nullable', 'string'],
            'price'        => ['required', 'numeric', 'min:0'],
            'is_available' => ['nullable', 'boolean'],
        ]);

        $data['is_available'] = $request->has('is_available');
        $menu->update($data);
        return back()->with('success', 'Menu berhasil diperbarui.');
    }

    public function menuDestroy(Menu $menu)
    {
        $menu->delete();
        return back()->with('success', 'Menu berhasil dihapus.');
    }
}
