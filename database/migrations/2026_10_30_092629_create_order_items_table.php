<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('menu_id')->nullable()->constrained('menus')->nullOnDelete();
            $table->string('name');          // snapshot name at time of order
            $table->integer('quantity')->default(1);
            $table->decimal('price', 12, 2)->default(0);    // unit price snapshot
            $table->decimal('subtotal', 12, 2)->default(0); // quantity * price
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
