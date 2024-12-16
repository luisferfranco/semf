<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
  protected $fillable = [
    'nombre',
    'asignada_a',
    'asignada_por',
    'tema_id',
    'fecha_inicio',
    'fecha_compromiso',
    'descripcion',
    'tipo',
    'estado',
  ];

  public function asignadaA() {
    return $this->belongsTo(User::class, 'asignada_a');
  }
  public function asignadaPor() {
    return $this->belongsTo(User::class, 'asignada_por');
  }
  public function tema() {
    return $this->belongsTo(Tema::class);
  }
  public function tareasHijas() {
    return $this->hasMany(Tarea::class, 'tarea_padre');
  }
  public function tareaPadre() {
    return $this->belongsTo(Tarea::class, 'tarea_padre');
  }
  public function acciones() {
    return $this->hasMany(Accion::class);
  }
}
