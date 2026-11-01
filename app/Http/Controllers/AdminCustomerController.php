<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminCustomerController extends Controller
{
    public function index()
    {
        // Mengambil user dengan role customer beserta total frekuensi pesanan & sum total harga pesanan yang selesai
        $customers = User::where('role', 'customer')
            ->withCount(['orders' => function ($query) {
                $query->where('status', 'completed');
            }])
            ->withSum(['orders' => function ($query) {
                $query->where('status', 'completed');
            }], 'total_price')
            ->latest()
            ->paginate(10);

        return view('admin.customers.index', compact('customers'));
    }
}