<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tema extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'estado', 'proyecto_id'];

    public function proyecto() {
      return $this->belongsTo(Proyecto::class);
    }
    public function tareas() {
      return $this->hasMany(Tarea::class);
    }
}
