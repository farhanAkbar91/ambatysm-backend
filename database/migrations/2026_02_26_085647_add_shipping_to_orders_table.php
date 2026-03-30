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
            $table->text('shipping_address')->nullable()->after('status');
            $table->string('city_id')->nullable()->after('shipping_address'); // ID Kota dari RajaOngkir
            $table->string('courier')->nullable()->after('city_id'); // Misal: jne, jnt, sicepat
            $table->decimal('shipping_cost', 12, 2)->default(0)->after('courier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_address', 'city_id', 'courier', 'shipping_cost']);
        });
    }
};
