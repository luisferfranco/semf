<?php

use App\Models\Tema;
use App\Models\Proyecto;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {
  public $proyecto = null;
  public $tema = null;

  #[On('proyecto-actualizado')]
  public function actualizarProyecto($value) {
    $this->proyecto = Proyecto::find($value);
    $this->tema = null;
  }

  #[On('tema-actualizado')]
  public function actualizarTema($value) {
    $this->tema = Tema::find($value);
  }
}; ?>

<div>
  <div class="flex justify-start space-x-2">
    <livewire:selectProyecto class="flex-grow" />
    <livewire:selectTema class="flex-grow" />
  </div>

  <h1>Proyecto {{ $proyecto }} - Tema {{ $tema }}</h1>
</div>
