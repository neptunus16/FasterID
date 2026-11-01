<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    /**
     * Tampilkan seluruh daftar pesanan (dengan filter status & pencarian).
     */
    public function index(Request $request)
    {
        $status = $request->query('status');

        $query = Order::with(['customer', 'driver', 'store', 'items'])
            ->latest();

        if ($status && in_array($status, ['pending', 'processing', 'delivering', 'completed', 'cancelled'])) {
            $query->where('status', $status);
        }

        $orders = $query->paginate(15)->withQueryString();

        // Hitungan status untuk tab filter
        $counts = [
            'all'        => Order::count(),
            'pending'    => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'delivering' => Order::where('status', 'delivering')->count(),
            'completed'  => Order::where('status', 'completed')->count(),
            'cancelled'  => Order::where('status', 'cancelled')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'counts', 'status'));
    }

    /**
     * Tampilkan detail rincian satu pesanan.
     */
    public function show(Order $order)
    {
        $order->load(['customer', 'driver', 'store', 'items']);
        $availableDrivers = User::where('role', 'driver')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.orders.show', compact('order', 'availableDrivers'));
    }

    /**
     * Perbarui status pesanan secara manual dari panel admin.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status'    => ['required', 'in:pending,processing,delivering,completed,cancelled'],
            'driver_id' => ['nullable', 'exists:users,id'],
        ]);

        $updateData = ['status' => $validated['status']];

        if ($request->has('driver_id')) {
            $updateData['driver_id'] = $validated['driver_id'];
        }

        $order->update($updateData);

        return back()->with('success', "Status pesanan #{$order->id} berhasil diubah menjadi: " . ucfirst($validated['status']));
    }
}

