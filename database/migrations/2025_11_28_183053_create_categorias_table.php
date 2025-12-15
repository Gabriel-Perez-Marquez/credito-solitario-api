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
<<<<<<<< HEAD:database/migrations/2025_11_27_095759_create_clientes_table.php
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellidos');
            $table->string('telefono');
            $table->string('email');
            $table->foreignId('direccion_id')->constrained()->cascadeOnDelete();
========
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->boolean('activa')->default(true);
>>>>>>>> f5deffc4c6977d6e32cc07ae4f4347c857157089:database/migrations/2025_11_28_183053_create_categorias_table.php
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
<<<<<<<< HEAD:database/migrations/2025_11_27_095759_create_clientes_table.php
        Schema::dropIfExists('clientes');
========
        Schema::dropIfExists('categorias');
>>>>>>>> f5deffc4c6977d6e32cc07ae4f4347c857157089:database/migrations/2025_11_28_183053_create_categorias_table.php
    }
};
