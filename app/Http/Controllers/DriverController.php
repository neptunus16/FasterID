<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverController extends Controller
{
    // ── Pending Notice ────────────────────────────────────────────────────────

    public function pending()
    {
        return view('driver.pending');
    }

    // ── Dashboard ─────────────────────────────────────────────────────────────

    public function dashboard()
    {
        $driver = Auth::user();

        $activeOrder = Order::where('driver_id', $driver->id)
            ->whereIn('status', ['processing', 'delivering'])
            ->with('customer', 'store', 'items')
            ->first();

        // Pesanan tersedia hanya tampil jika driver sedang online
        $availableOrders = collect();
        if ($driver->driver_status === 'online') {
            $availableOrders = Order::whereNull('driver_id')
                ->where('status', 'pending')
                ->with('customer', 'store')
                ->latest()
                ->get();
        }

        // Pendapatan driver hari ini: dihitung dari ongkos kirim pesanan yang diselesaikan
        $todayEarnings = Order::where('driver_id', $driver->id)
            ->where('status', 'completed')
            ->whereDate('updated_at', today())
            ->sum('shipping_fee');

        return view('driver.dashboard', compact(
            'driver', 'activeOrder', 'availableOrders', 'todayEarnings'
        ));
    }

    // ── Cycle driver_status: offline → online → resting → offline → ... ───────

    public function toggleStatus()
    {
        $driver = Auth::user();

        $next = match ($driver->driver_status) {
            'offline'  => 'online',
            'online'   => 'resting',
            'resting'  => 'offline',
            default    => 'online',
        };

        $driver->update([
            'driver_status' => $next,
            'is_online'     => $next !== 'offline', // sinkronisasi kolom lama
        ]);

        $labels = ['online' => '🟢 Aktif', 'resting' => '🟡 Istirahat', 'offline' => '⚫ Offline'];
        return back()->with('success', "Status diubah ke {$labels[$next]}.");
    }

    // ── Set status secara eksplisit (via tombol di dashboard) ─────────────────

    public function setStatus(Request $request)
    {
        $request->validate([
            'driver_status' => ['required', 'in:online,resting,offline'],
        ]);

        $status = $request->driver_status;
        Auth::user()->update([
            'driver_status' => $status,
            'is_online'     => $status !== 'offline',
        ]);

        $labels = ['online' => '🟢 Aktif', 'resting' => '🟡 Istirahat', 'offline' => '⚫ Offline'];
        return back()->with('success', "Status diubah ke {$labels[$status]}.");
    }

    // ── Take Order ────────────────────────────────────────────────────────────

    public function takeOrder(Order $order)
    {
        $driver = Auth::user();

        if ($driver->driver_status !== 'online') {
            return back()->withErrors(['error' => 'Anda harus berstatus Aktif (Online) untuk mengambil pesanan.']);
        }

        if ($order->driver_id !== null || $order->status !== 'pending') {
            return back()->withErrors(['error' => 'Pesanan ini sudah diambil driver lain.']);
        }

        $order->update([
            'driver_id' => $driver->id,
            'status'    => 'processing',
        ]);

        return back()->with('success', 'Pesanan berhasil diambil!');
    }

    // ── Update Order Status ───────────────────────────────────────────────────

    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status'        => ['required', 'in:delivering,completed'],
            'shopping_cost' => ['nullable', 'numeric', 'min:0'],
        ]);

        abort_if($order->driver_id !== Auth::id(), 403);

        $updateData = ['status' => $request->status];

        // Jika pesanan custom dan status diselesaikan (completed)
        if ($order->is_custom && $request->status === 'completed') {
            $request->validate([
                'shopping_cost' => ['required', 'numeric', 'min:0'],
            ], [
                'shopping_cost.required' => 'Wajib memasukkan total harga belanjaan riil dari toko non-mitra.',
            ]);

            $shoppingCost = (float) $request->shopping_cost;
            $shippingCost = (float) ($order->shipping_fee ?? 10000);
            $totalPrice   = $shoppingCost + $shippingCost;

            $updateData['shopping_cost'] = $shoppingCost;
            $updateData['total_amount']  = $shoppingCost;
            $updateData['total_price']   = $totalPrice;
        }

        $order->update($updateData);

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    // ── History ───────────────────────────────────────────────────────────────

    public function history()
    {
        $orders = Order::where('driver_id', Auth::id())
            ->where('status', 'completed')
            ->with('customer', 'store')
            ->latest()
            ->paginate(15);

        return view('driver.history', compact('orders'));
    }
}
