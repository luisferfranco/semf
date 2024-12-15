<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
  protected $fillable = ['nombre', 'descripcion', 'estado', 'user_id'];

  public function user() {
    return $this->belongsTo(User::class);
  }
  public function temas() {
    return $this->hasMany(Tema::class);
  }
}
