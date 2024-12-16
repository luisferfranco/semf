<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accion extends Model
{
  protected $table = 'acciones';
  protected $fillable = [
    'tarea_id',
    'usuario_id',
    'fecha',
    'descripcion',
  ];

  public function tarea() {
    return $this->belongsTo(Tarea::class);
  }
  public function usuario() {
    return $this->belongsTo(User::class);
  }
}
