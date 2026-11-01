<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * driver_status  : ketersediaan driver secara real-time
     *   'online'     → Aktif, siap menerima pesanan
     *   'resting'    → Istirahat sementara, tidak menerima pesanan
     *   'offline'    → Tidak bertugas
     *
     * Kolom is_online yang lama tetap dipertahankan untuk kompatibilitas
     * (nilainya akan disinkronkan: online/resting = true, offline = false).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('driver_status', ['online', 'resting', 'offline'])
                  ->default('offline')
                  ->after('is_online');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('driver_status');
        });
    }
};
