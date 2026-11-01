<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('store_id')->nullable()->change();
            $table->boolean('is_custom')->default(false)->after('store_id');
            $table->string('store_name')->nullable()->after('is_custom');
            $table->text('store_address')->nullable()->after('store_name');
            $table->text('notes')->nullable()->after('store_address');
            $table->decimal('shopping_cost', 12, 2)->default(0)->after('total_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['is_custom', 'store_name', 'store_address', 'notes', 'shopping_cost']);
            $table->foreignId('store_id')->nullable(false)->change();
        });
    }
};
