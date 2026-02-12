<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
        });

        DB::statement('ALTER TABLE pedidos MODIFY cliente_id BIGINT UNSIGNED NULL');

        Schema::table('pedidos', function (Blueprint $table) {
            $table->foreign('cliente_id')->references('id')->on('clientes')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
        });

        DB::statement('ALTER TABLE pedidos MODIFY cliente_id BIGINT UNSIGNED NOT NULL');

        Schema::table('pedidos', function (Blueprint $table) {
            $table->foreign('cliente_id')->references('id')->on('clientes');
        });
    }
};
