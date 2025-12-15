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
        Schema::create('direccions', function (Blueprint $table) {
            $table->id();
<<<<<<<< HEAD:database/migrations/2025_11_27_100041_create_direccions_table.php
========
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->foreignId('categoria_id')->constrained('categorias');
            $table->decimal('precio', 10, 2); 
            $table->boolean('activo')->default(true);
            $table->decimal('descuento', 5, 2)->default(0);
>>>>>>>> f5deffc4c6977d6e32cc07ae4f4347c857157089:database/migrations/2025_11_28_183103_create_productos_table.php
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direccions');
    }
};
