<?php

use App\Models\Proyecto;
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Validate;

new class extends Component {
  public $proyectos;

  #[Rule('required', 'max:255')]
  public $nombre;

  public $headers;

  public function mount() {
    $this->proyectos = Proyecto::all();

    $this->headers = [
      ['key' => 'id', 'label'=>"ID", "class" => "w-2", "disableLink" => true],
      ['key' => 'nombre', 'label'=>"Nombre"],
    ];
  }

  public function store() {
    $this->validate();

    Proyecto::create([
      'user_id' => auth()->id(),
      'nombre' => $this->nombre,
    ]);

    $this->nombre = '';
    $this->proyectos = Proyecto::all();
  }
}; ?>

<div>
  <x-h1 label="Proyectos" />

  <x-form wire:submit='store' class="mb-4">
    <x-input
      wire:model='nombre'
      placeholder='Nombre del Proyecto'
      autofocus
      />
  </x-form>


  @if ($proyectos->isEmpty())
    <x-alert title="No hay proyector registrados" icon="bxs.info-circle" class="alert-warning" />
  @else
    <x-card class="text-content-primary">
      <x-table
        :headers="$headers"
        :rows="$proyectos"
        {{-- :link="route('proyecto.show', ['proyecto' => ['id']])" --}}
        striped
        >
        @scope('actions', $proyecto)
          <div class="flex space-x-1">
            <x-button
              icon="bxs.trash"
              class="btn-circle btn-ghost btn-xs"
              tooltip-left="Eliminar"
              wire:click="delete({{ $proyecto->id }})"
              />
            <x-button
              icon="bxs.edit"
              class="btn-circle btn-ghost btn-xs"
              tooltip-left="Detalles"
              wire:click="edit({{ $proyecto->id }})"
              />
          </div>
        @endscope
      </x-table>
    </x-card>
  @endif
</div>
