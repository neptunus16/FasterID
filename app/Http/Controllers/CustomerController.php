<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    // ── Dashboard ─────────────────────────────────────────────────────────────

    public function dashboard(Request $request)
    {
        // Ambil input kata kunci pencarian dari request GET
        $search = $request->input('search');

        // Query data store dengan filter pencarian nama atau alamat (jika ada)
        $stores = Store::where('is_active', true)
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('address', 'like', "%{$search}%");
            })
            ->latest()
            ->get();

        $recentOrders = Auth::user()
            ->ordersAsCustomer()
            ->with('store', 'items')
            ->latest()
            ->take(5)
            ->get();

        return view('customer.dashboard', compact('stores', 'recentOrders', 'search'));
    }
    // ── Store Detail ──────────────────────────────────────────────────────────

    public function storeDetail(Store $store)
    {
        abort_if(! $store->is_active, 404);
        $menus = $store->menus()->where('is_available', true)->get();
        return view('customer.store', compact('store', 'menus'));
    }

    // ── Place Order ───────────────────────────────────────────────────────────

    public function placeOrder(Request $request, Store $store)
    {
        $request->validate([
            'items'              => ['required', 'array', 'min:1'],
            'items.*.menu_id'    => ['required', 'exists:menus,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:0'],
            'delivery_address'   => ['required', 'string'],
            'customer_note'      => ['nullable', 'string', 'max:500'],
            'payment_method'     => ['required', 'in:cash,transfer'],
            'shipping_fee'       => ['nullable', 'numeric', 'min:0'],
        ]);

        // Filter item dengan quantity > 0
        $selectedItems = array_filter($request->items, fn ($i) => isset($i['quantity']) && (int)$i['quantity'] > 0);

        if (empty($selectedItems)) {
            return back()->withErrors(['error' => 'Pilih minimal 1 item menu yang ingin dipesan.'])->withInput();
        }

        DB::transaction(function () use ($request, $store, $selectedItems) {
            $totalAmount = 0;
            $orderItems  = [];

            foreach ($selectedItems as $item) {
                $menu     = $store->menus()->where('is_available', true)->findOrFail($item['menu_id']);
                $quantity = (int)$item['quantity'];
                $price    = (float)$menu->price;
                $subtotal = $price * $quantity;
                $totalAmount += $subtotal;

                $orderItems[] = [
                    'menu_id'  => $menu->id,
                    'name'     => $menu->name,
                    'quantity' => $quantity,
                    'price'    => $price,
                    'subtotal' => $subtotal,
                ];
            }

            // Standar ongkos kirim FasterID (default Rp 8.000 jika tidak dispesifikasikan)
            $shippingFee = $request->filled('shipping_fee') ? (float)$request->shipping_fee : 8000.0;

            // Pastikan grand total minimal mengambil nilai subtotal produk
            $totalPrice = $totalAmount + max(0, $shippingFee);
            if ($totalPrice <= 0 && $totalAmount > 0) {
                $totalPrice = $totalAmount;
            }

            $order = Order::create([
                'customer_id'      => Auth::id(),
                'store_id'         => $store->id,
                'total_amount'     => $totalAmount,
                'shipping_fee'     => $shippingFee,
                'total_price'      => $totalPrice,
                'delivery_address' => $request->delivery_address,
                'customer_note'    => $request->customer_note,
                'payment_method'   => $request->payment_method,
                'status'           => 'pending',
            ]);

            $order->items()->createMany($orderItems);
        });

        return redirect()->route('customer.orders')->with('success', 'Pesanan berhasil dibuat!');
    }

    // ── Custom Order (Toko Non-Mitra) ─────────────────────────────────────────

    public function createCustomOrder()
    {
        return view('customer.custom-order');
    }

    public function storeCustomOrder(Request $request)
    {
        $validated = $request->validate([
            'store_name'       => ['required', 'string', 'max:150'],
            'store_address'    => ['required', 'string', 'max:500'],
            'notes'            => ['required', 'string', 'max:2000'],
            'delivery_address' => ['required', 'string', 'max:500'],
            'customer_note'    => ['nullable', 'string', 'max:500'],
            'payment_method'   => ['required', 'in:cash,transfer'],
        ]);

        // Ongkos kirim flat untuk pesanan custom (Rp 10.000)
        $shippingFee = 10000;

        $order = Order::create([
            'customer_id'      => Auth::id(),
            'store_id'         => null,
            'is_custom'        => true,
            'store_name'       => $validated['store_name'],
            'store_address'    => $validated['store_address'],
            'notes'            => $validated['notes'],
            'total_amount'     => 0, // Belanjaan akan ditentukan driver riil di toko
            'shopping_cost'    => 0,
            'shipping_fee'     => $shippingFee,
            'total_price'      => $shippingFee,
            'delivery_address' => $validated['delivery_address'],
            'customer_note'    => $validated['customer_note'],
            'payment_method'   => $validated['payment_method'],
            'status'           => 'pending',
        ]);

        return redirect()->route('customer.order.detail', $order)
            ->with('success', 'Pesanan toko non-mitra berhasil dibuat! Driver akan segera mencari pesanan Anda.');
    }

    // ── Order History ─────────────────────────────────────────────────────────

    public function orders()
    {
        $orders = Auth::user()
            ->ordersAsCustomer()
            ->with('store', 'items', 'driver')
            ->latest()
            ->paginate(10);

        return view('customer.orders', compact('orders'));
    }

    public function orderDetail(Order $order)
    {
        abort_if($order->customer_id !== Auth::id(), 403);
        $order->load('store', 'items.menu', 'driver');
        return view('customer.order-detail', compact('order'));
    }
}
