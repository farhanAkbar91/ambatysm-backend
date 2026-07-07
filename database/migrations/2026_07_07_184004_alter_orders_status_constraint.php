<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (config('database.default') === 'pgsql') {
            DB::statement("ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_status_check");
            DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_status_check CHECK (status IN ('pending', 'waiting_confirmation', 'paid', 'processing', 'shipped', 'completed', 'cancelled', 'review', 'diterima', 'ditolak', 'dikerjakan'))");
        } else {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('status')->default('pending')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (config('database.default') === 'pgsql') {
            DB::statement("ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_status_check");
            DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_status_check CHECK (status IN ('pending', 'waiting_confirmation', 'paid', 'processing', 'completed', 'cancelled'))");
        }
    }
};
