<?php

use App\Models\Tema;
use App\Models\Proyecto;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {
  public $proyecto = null;
  public $temas;
  public $id = null;        // id de la tarea (select)
  public $tema = null;      // Objeto tema para crear o editar

  // Formulario
  public $nombre = null;
  public $descripcion = null;
  public $modal = false;

  #[On('proyecto-actualizado')]
  public function actualizarProyecto($value) {
    if ($value) {
      $this->proyecto = $value;
      $this->getData();
    } else {
      $this->temas = null;
    }
  }

  public function getData() {
    $this->temas = Tema::where('proyecto_id', $this->proyecto)
      ->orderBy('nombre')
      ->get();
  }

  public function crear() {
    $this->id = null;
    $this->nombre = null;
    $this->modal = true;
    $this->tema = new Tema();
  }

  public function guardar() {
    $this->validate([
      'nombre' => 'required',
    ]);

    if ($this->id) {
      $this->tema = Tema::find($this->id);
    }

    $this->tema->nombre = $this->nombre;
    $this->tema->descripcion = $this->descripcion;
    $this->tema->proyecto_id = $this->proyecto;
    $this->tema->save();

    $this->getData();
    $this->modal = false;
  }

  public function editar($id) {
    $this->id = $id;
    $this->tema = Tema::find($id);
    $this->nombre = $this->tema->nombre;
    $this->modal = true;
  }

  public function updatedId($value) {
    $this->dispatch('tema-actualizado', $value);
  }

}; ?>

<div>
  <x-modal wire:model='modal' class="backdrop-blur">
    <form wire:submit.prevent='guardar'>
      <x-input wire:model='nombre'
                label="Nombre del tema"
                placeholder="Nombre del tema"
                />
      <x-textarea wire:model='descripcion'
                  label="Descripción"
                  rows="5"
                  hint="Usa Markdown"
                  />
      <div class="mt-4">
        <x-button label="Guardar"
                  type="submit"
                  icon="bxs.save"
                  class="btn-primary"
                  />
        <x-button label="Cancelar"
                  icon="bxs.x-circle"
                  class="ml-1 btn-error"
                  wire:click="$set('modal', false)"
                  />
      </div>
    </form>
  </x-modal>

  @if ($temas)
    <x-select wire:model.live='id'
            label="Tema"
            option-value="id"
            option-label="nombre"
            :options="$temas"
            placeholder="Seleccione un tema"
            >
      <x-slot:append>
        <x-button label=""
                  icon="bxs.edit"
                  class="rounded-s-none rounded-e-none btn-accent"
                  wire:click="editar({{ $id }})"
                  />
        {{--  Add `rounded-s-none` (RTL support) --}}
        <x-button label=""
                  icon="bxs.plus-circle"
                  class="rounded-s-none btn-primary"
                  wire:click="crear"
                  />
      </x-slot:append>
    </x-select>
  @endif
</div>
