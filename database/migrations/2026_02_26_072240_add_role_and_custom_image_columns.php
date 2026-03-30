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
        // Tambah role di tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'customer'])->default('customer')->after('password');
        });

        // Tambah file_desain di tabel orders
        Schema::table('orders', function (Blueprint $table) {
            $table->string('custom_image')->nullable()->after('custom_notes');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('custom_image');
        });
    }
};
