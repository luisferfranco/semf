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
    Schema::create('tareas', function (Blueprint $table) {
      $table->id();

      $table->string('nombre');
      $table->foreignId('asignada_a')->constrained('users');
      $table->foreignId('asignada_por')->constrained('users');
      $table->foreignIdFor(\App\Models\Tema::class)->constrained();
      $table->date('fecha_inicio')->nullable();
      $table->date('fecha_compromiso')->nullable();
      $table->text('descripcion')->nullable();
      $table->string('tipo')->default('administrativo');
      $table->string('estado')->default('en proceso');
      $table->foreignIdFor(\App\Models\Tarea::class, 'tarea_padre_id')
        ->nullable()
        ->constrained();

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('tareas');
  }
};
