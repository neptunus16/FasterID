<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'driver_id', 'store_id',
        'is_custom', 'store_name', 'store_address', 'notes',
        'total_amount', 'shopping_cost', 'shipping_fee', 'total_price', 'status',
        'delivery_address', 'customer_note', 'payment_method',
    ];

    protected function casts(): array
    {
        return [
            'is_custom'     => 'boolean',
            'total_amount'  => 'decimal:2',
            'shopping_cost' => 'decimal:2',
            'shipping_fee'  => 'decimal:2',
            'total_price'   => 'decimal:2',
        ];
    }

    /**
     * Nama toko tampilan (jika pesanan custom, pakai store_name; jika reguler pakai store->store_name).
     */
    public function getDisplayStoreNameAttribute(): string
    {
        if ($this->is_custom) {
            return $this->store_name ?: 'Toko Non-Mitra';
        }
        return $this->store->store_name ?? 'Toko Tidak Ditemukan';
    }

    /**
     * Alamat toko tampilan.
     */
    public function getDisplayStoreAddressAttribute(): ?string
    {
        if ($this->is_custom) {
            return $this->store_address;
        }
        return $this->store->address ?? null;
    }

    /**
     * Accessor untuk grand_total (alias/kalkulasi aman dari total pembayaran).
     */
    protected function grandTotal(): Attribute
    {
        return Attribute::make(
            get: function () {
                $savedTotal = (float) ($this->total_price ?? 0);
                if ($savedTotal > 0) {
                    return $savedTotal;
                }

                $shipping = (float) ($this->shipping_fee ?? 0);

                if ($this->is_custom) {
                    $shopping = (float) ($this->shopping_cost ?? 0);
                    return $shopping + $shipping;
                }

                $subtotal = (float) ($this->total_amount ?? 0);
                $calculated = $subtotal + $shipping;
                return $calculated > 0 ? $calculated : $subtotal;
            }
        );
    }

    /**
     * Accessor total_price agar selalu menghasilkan angka valid.
     */
    protected function totalPrice(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $val = (float) ($value ?? 0);
                if ($val > 0) {
                    return $val;
                }

                $shipping = (float) ($this->shipping_fee ?? 0);

                if ($this->is_custom) {
                    $shopping = (float) ($this->shopping_cost ?? 0);
                    return $shopping + $shipping;
                }

                $subtotal = (float) ($this->total_amount ?? 0);
                $calculated = $subtotal + $shipping;

                return $calculated > 0 ? $calculated : $subtotal;
            },
            set: function ($value) {
                return (float) $value;
            }
        );
    }

    // ── Relationships ─────────────────────────────────────────────────────────
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function messages()
    {
        return $this->hasMany(OrderMessage::class)->orderBy('id', 'asc');
    }

    // ── Status helpers ────────────────────────────────────────────────────────
    public function isPending(): bool    { return $this->status === 'pending'; }
    public function isProcessing(): bool { return $this->status === 'processing'; }
    public function isDelivering(): bool { return $this->status === 'delivering'; }
    public function isCompleted(): bool  { return $this->status === 'completed'; }
    public function isCancelled(): bool  { return $this->status === 'cancelled'; }

    public function statusBadge(): array
    {
        return match ($this->status) {
            'pending'    => ['label' => 'Menunggu Konfirmasi', 'color' => 'bg-yellow-100 text-yellow-800 border-yellow-200', 'dot' => 'bg-yellow-500'],
            'processing' => ['label' => 'Diproses Toko/Driver', 'color' => 'bg-blue-100 text-blue-800 border-blue-200', 'dot' => 'bg-blue-500'],
            'delivering' => ['label' => 'Sedang Diantar', 'color' => 'bg-orange-100 text-orange-800 border-orange-200', 'dot' => 'bg-orange-500'],
            'completed'  => ['label' => 'Selesai', 'color' => 'bg-green-100 text-green-800 border-green-200', 'dot' => 'bg-green-500'],
            'cancelled'  => ['label' => 'Dibatalkan', 'color' => 'bg-red-100 text-red-800 border-red-200', 'dot' => 'bg-red-500'],
            default      => ['label' => ucfirst($this->status), 'color' => 'bg-gray-100 text-gray-800 border-gray-200', 'dot' => 'bg-gray-400'],
        };
    }
}
