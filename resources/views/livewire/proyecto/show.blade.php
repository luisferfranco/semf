<?php

use App\Models\Tema;
use App\Models\Proyecto;
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;

new class extends Component {
  public $proyecto;
  public $descripcion, $headers;
  public $isEditing = false;

  #[Rule('required', 'max:255')]
  public $nombre;

  public function mount(?Proyecto $proyecto) {
    $this->proyecto = $proyecto;
    $this->descripcion = $proyecto->descripcion;

    $this->headers = [
      ['key' => 'id', 'label' => 'ID', 'class' => 'w-2', 'disableLink' => true],
      ['key' => 'nombre', 'label' => 'Tema'],
    ];
  }

  public function update() {
    $this->proyecto->update([
      'descripcion' => $this->descripcion,
    ]);

    $this->isEditing = false;
  }

  public function cancelar() {
    $this->descripcion = $this->proyecto->descripcion;
    $this->isEditing = false;
  }

  public function store() {
    $this->validate();

    Tema::create([
      'proyecto_id' => $this->proyecto->id,
      'nombre' => $this->nombre,
    ]);

    $this->nombre = '';
    $this->proyecto = Proyecto::find($this->proyecto->id);
  }
}; ?>

<div>
  <x-h1>
    Proyecto <span class="text-accent">{{ $proyecto->nombre }}</span>
  </x-h1>

  <x-card class="mb-4 text-content-primary" shadow>
    <x-slot:title>
      <span class="text-accent">{{ $proyecto->nombre }}</span>
    </x-slot:title>

    {{-- Edición de la descripción --}}
    <div x-data="{isEditing: $wire.entangle('isEditing')}">
      <div x-show="!isEditing">
        @if ($proyecto->descripcion)
          {{ $proyecto->descripcion }}
        @endif

        <div class="flex justify-end">
          <x-button
            icon="bx.edit"
            class="btn btn-primary"
            label="Editar"
            @click="isEditing = true"
            />
        </div>
      </div>

      <div x-show="isEditing" x-cloak>
        <x-form wire:submit='update' class="mb-4">
          <x-textarea
            wire:model='descripcion'
            placeholder='Descripción del Proyecto'
            hint="Utiliza Markdown"
            rows="5"
            />
          <div class="flex justify-end space-x-2">
            <x-button
              type="submit"
              icon="bx.save"
              class="btn btn-primary"
              label="Guardar"
              />
            <x-button
              icon="bx.save"
              class="btn btn-error"
              label="Cancelar"
              wire:click='cancelar'
              />
          </div>
        </x-form>
      </div>
    </div>
  </x-card>

  <x-form wire:submit='store' class="mb-4">
    <x-input
      wire:model='nombre'
      placeholder='Nombre del Tema'
      autofocus
      />
  </x-form>

  @if ($proyecto->temas->count() == 0)
    <x-alert title="No hay temas registrados" icon="bxs.info-circle" class="alert-warning" />
  @else
    <x-card class="text-content-primary">
      <x-table
        :headers="$headers"
        :rows="$proyecto->temas"
        link="/tema/{id}"
        striped
        >
        @scope('header_id', $header)
          <span class="text-primary-content">{{ $header['label'] }}</span>
        @endscope
        @scope('header_nombre', $header)
          <span class="text-primary-content">{{ $header['label'] }}</span>
        @endscope

        @scope('actions', $tema)
          <div class="flex space-x-1">
            <x-button
              icon="bxs.trash"
              class="btn-circle btn-ghost btn-xs text-error"
              tooltip-left="Eliminar"
              wire:click="delete({{ $tema->id }})"
              />
          </div>
        @endscope
      </x-table>
    </x-card>
  @endif
</div>
