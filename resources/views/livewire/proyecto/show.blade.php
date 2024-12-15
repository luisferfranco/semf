<?php

use App\Models\Proyecto;
use Livewire\Volt\Component;

new class extends Component {
  public $proyecto;
  public $descripcion;
  public $isEditing = false;

  public function mount(?Proyecto $proyecto) {
    $this->proyecto = $proyecto;
    $this->descripcion = $proyecto->descripcion;
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
}; ?>

<div>
  <x-h1>
    Proyecto <span class="text-accent">{{ $proyecto->nombre }}</span>
  </x-h1>

  <x-card class="text-content-primary" shadow>
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


</div>
