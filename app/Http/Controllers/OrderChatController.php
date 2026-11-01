<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderChatController extends Controller
{
    /**
     * Memeriksa otorisasi user terhadap pesanan:
     * Hanya Customer pemesan, Driver yang ditugaskan, atau Admin yang diizinkan.
     */
    protected function authorizeAccess(Order $order): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(401, 'Unauthenticated');
        }

        $isAdmin = $user->role === 'admin';
        $isCustomer = $order->customer_id === $user->id;
        $isDriver = $order->driver_id === $user->id;

        if (! ($isAdmin || $isCustomer || $isDriver)) {
            abort(403, 'Anda tidak memiliki hak akses ke obrolan pesanan ini.');
        }
    }

    /**
     * Ambil daftar pesan chat untuk pesanan tertentu.
     * Mengembalikan JSON untuk AJAX polling.
     */
    public function index(Request $request, Order $order)
    {
        $this->authorizeAccess($order);

        $afterId = $request->query('after_id');

        $query = $order->messages()->with('sender:id,name,role');

        if ($afterId) {
            $query->where('id', '>', (int)$afterId);
        }

        $messages = $query->get()->map(function ($msg) {
            return [
                'id'          => $msg->id,
                'order_id'    => $msg->order_id,
                'sender_id'   => $msg->sender_id,
                'sender_name' => $msg->sender->name ?? 'User',
                'sender_role' => $msg->sender->role ?? 'user',
                'is_me'       => $msg->sender_id === Auth::id(),
                'message'     => $msg->message,
                'time'        => $msg->created_at ? $msg->created_at->format('H:i') : now()->format('H:i'),
            ];
        });

        return response()->json([
            'status'   => 'success',
            'order_id' => $order->id,
            'messages' => $messages,
        ]);
    }

    /**
     * Kirim pesan baru ke pesanan.
     */
    public function store(Request $request, Order $order)
    {
        $this->authorizeAccess($order);

        $user = Auth::user();

        // Admin hanya mode read-only sesuai spesifikasi, customer/driver yang boleh kirim pesan
        if ($user->role === 'admin') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Admin hanya memiliki mode pantau (Read-Only) pada chat pesanan ini.'
            ], 403);
        }

        // Chat hanya aktif jika order sudah diambil driver (processing / delivering / completed)
        if (! in_array($order->status, ['processing', 'delivering', 'completed'])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Obrolan hanya aktif setelah pesanan diterima oleh driver.'
            ], 422);
        }

        $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $msg = OrderMessage::create([
            'order_id'   => $order->id,
            'sender_id'  => $user->id,
            'message'    => trim($request->message),
            'created_at' => now(),
        ]);

        $msg->load('sender:id,name,role');

        return response()->json([
            'status'  => 'success',
            'message' => [
                'id'          => $msg->id,
                'order_id'    => $msg->order_id,
                'sender_id'   => $msg->sender_id,
                'sender_name' => $msg->sender->name ?? 'User',
                'sender_role' => $msg->sender->role ?? 'user',
                'is_me'       => true,
                'message'     => $msg->message,
                'time'        => $msg->created_at->format('H:i'),
            ]
        ]);
    }
}

