<?php

use Mary\Traits\Toast;
use App\Models\Proyecto;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;

new class extends Component {
  use Toast;

  public $proyectos;
  public $proyecto;
  public $id=null;
  public $modal=false;

  // Formulario
  public $nombre;
  public $descripcion;

  public function mount() {
    $this->proyectos = Proyecto::orderBy('nombre')->get();
  }

  public function crear() {
    $this->proyecto = new Proyecto();
    $this->nombre = '';
    $this->descripcion = '';
    $this->modal = true;
  }

  public function guardar() {
    $this->validate([
      'nombre' => 'required',
    ]);

    if ($this->id) {
      $proyecto = Proyecto::find($this->id);
      $proyecto->nombre = $this->nombre;
      $proyecto->descripcion = $this->descripcion;
      $proyecto->save();

      $this->success(
        'Proyecto actualizado',
        timeout: 3000,
      );
    } else {
      $proyecto = new Proyecto();
      $proyecto->nombre = $this->nombre;
      $proyecto->descripcion = $this->descripcion;
      $proyecto->user_id = auth()->id();
      $proyecto->save();

      $this->success(
        'Proyecto creado',
        timeout: 3000,
      );
    }

    $this->proyectos = Proyecto::orderBy('nombre')->get();
    $this->modal = false;
    // $this->id = $this->proyecto->id;
  }

  public function editar() {
    if ($this->id) {
      $proyecto = Proyecto::find($this->id);
      $this->nombre = $proyecto->nombre;
      $this->descripcion = $proyecto->descripcion;
      $this->modal = true;
    }
  }

  public function updatedId($value) {
    $this->dispatch('proyecto-actualizado', $value);
  }
}; ?>

<div>

  {{-- Modal para crear el proyecto --}}
  <x-modal wire:model='modal' class="backdrop-blur">
    <form wire:submit.prevent='guardar'>
      <x-input wire:model='nombre'
                label="Nombre del Proyecto"
                autofocus
                />
      <x-textarea wire:model='descripcion'
                  label="Descripción"
                  rows="5"
                  hint="Utiliza Markdown"
                  />
      <x-button label="Guardar"
                type="submit"
                icon="bxs.save"
                class="mt-4 btn-primary"
                />
      <x-button label="Cancelar"
                icon="bxs.x-circle"
                class="ml-1 btn-error"
                wire:click="$set('modal', false)"
                />
    </form>
  </x-modal>

  <x-select wire:model.live='id'
          label="Proyecto"
          option-value="id"
          option-label="nombre"
          :options="$proyectos"
          placeholder="Seleccione un proyecto"
          >
    <x-slot:append>
      <x-button label=""
                icon="bxs.edit"
                class="rounded-s-none rounded-e-none btn-secondary"
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


</div>
