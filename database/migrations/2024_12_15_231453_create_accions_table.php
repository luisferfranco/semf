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
    Schema::create('acciones', function (Blueprint $table) {
      $table->id();

      $table->foreignIdFor(\App\Models\Tarea::class)->constrained();
      $table->foreignIdFor(\App\Models\User::class, 'usuario_id')->constrained();
      $table->date('fecha');
      $table->text('descripcion');

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('acciones');
  }
};
