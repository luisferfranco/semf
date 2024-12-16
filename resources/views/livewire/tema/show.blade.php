<?php

use App\Models\Tema;
use App\Models\User;
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;

new class extends Component {
  public $tema;
  public $isEditing = false;
  public $isCreating = false;

  public $descripcion, $nombre;
  public $estado;
  public $headers;
  public $users;

  // Variables del formulario
  #[Rule('required|max:255')]
  public $nombre_tarea;
  public $descripcion_tarea;
  #[Rule('required')]
  public $asignada_a;
  #[Rule('required')]
  public $fecha_compromiso;
  #[Rule('required')]
  public $tipo;

  public $opciones_tipo;

  public function mount(?Tema $tema = null) {
    $this->tema = $tema;
    $this->descripcion = $tema->descripcion;
    $this->nombre = $tema->nombre;
    $this->headers = [
      ['key' => 'id', 'label' => 'ID', 'class' => 'w-2', 'disableLink' => true],
      ['key' => 'nombre', 'label' => 'Tarea'],
      ['key' => 'asignada_a', 'label' => 'Asignada a'],
      ['key' => 'fecha_compromiso', 'label' => 'Fecha Compromiso'],
      ['key' => 'tipo', 'label' => 'Tipo'],
    ];

    $this->users = User::orderBy('name')->get();
    $this->opciones_tipo = [
      ['id' => 'administrativa', 'name' => 'Administrativa'],
      ['id' => 'operativa', 'name' => 'Operativa'],
    ];
  }

  public function update() {
    $this->tema->update([
      'nombre' => $this->nombre,
      'descripcion' => $this->descripcion,
    ]);

    $this->isEditing = false;
  }

  public function cancelar() {
    $this->descripcion = $this->tema->descripcion;
    $this->nombre = $this->tema->nombre;
    $this->isEditing = false;
  }

  public function store() {
    $data = $this->validate();
    info($data);

    $this->tema->tareas()->create([
      'nombre'            => $this->nombre_tarea,
      'asignada_a'        => $this->asignada_a,
      'asignada_por'      => auth()->id(),
      'tema_id'           => $this->tema->id,
      'descripcion'       => $this->descripcion_tarea,
      'fecha_compromiso'  => $this->fecha_compromiso,
      'tipo'              => $this->tipo,
    ]);

    $this->nombre_tarea       = '';
    $this->asignada_a         = null;
    $this->tema_id            = null;
    $this->fecha_compromiso   = null;
    $this->descripcion_tarea  = null;
    $this->tipo               = null;

    $this->isCreating         = false;
  }

  public function reset_tarea() {
    $this->nombre_tarea       = '';
    $this->asignada_a         = null;
    $this->tema_id            = null;
    $this->fecha_compromiso   = null;
    $this->descripcion_tarea  = null;
    $this->tipo               = null;

    $this->isCreating         = false;
  }


}; ?>

<div>
  <x-h1>
    PROYECTO <span class="text-accent">{{ $tema->proyecto->nombre }}</span>
    <br />
    <span class="text-sm">TEMA <span class="text-accent">{{ $tema->nombre }}</span></span>
  </x-h1>

  <x-card class="mb-4 text-content-primary" shadow>
    <x-slot:title>
      <span class="text-accent">{{ $tema->nombre }}</span>
    </x-slot:title>

    {{-- Edición de la descripción --}}
    <div x-data="{isEditing: $wire.entangle('isEditing')}">
      <div x-show="!isEditing">
        @if ($tema->descripcion)
          {{ $tema->descripcion }}
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
          <x-input
            wire:model='nombre'
            placeholder='Nombre del Tema'
            />
          <x-textarea
            wire:model='descripcion'
            placeholder='Descripción del Tema'
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

  {{-- Nueva Tarea --}}
  <div x-data="{isCreating: $wire.entangle('isCreating')}">
    <div x-show="!isCreating">
      <x-button
        icon="bxs.plus-circle"
        label="Nueva Tarea"
        class="mb-4 btn btn-primary"
        @click="isCreating = true"
        />
    </div>
    <div x-show="isCreating" x-cloak>
      <x-card title="Nueva Tarea" class="mb-4">
        <x-form wire:submit='store' class="mb-4">
          <x-input wire:model='nombre_tarea' placeholder='Nombre de la Tarea' autofocus />
          <div class="flex justify-between space-x-2">
            <div class="w-full">
              <x-select wire:model='asignada_a' :options="$users" icon="bxs.user-circle" label='Asignada a' placeholder="Asignar un responsable"/>
            </div>
            <div class="w-full">
              <x-datetime label="Fecha Compromiso" wire:model="fecha_compromiso" icon="bxs.calendar" />
            </div>
            <div class="w-full">
              <x-select label="Tipo" wire:model="tipo" :options="$opciones_tipo" icon="bx.transfer-alt" placeholder="Selecciona" />
            </div>
          </div>
          <x-textarea wire:model='descripcion_tarea' placeholder='Describe la tarea' rows="5" hint='Usa Markdown' />

          {{-- Guardar/Cancelar --}}
          <div class="flex justify-end mt-4 space-x-2">
            <x-button
              type="submit"
              icon="bx.save"
              class="btn btn-primary"
              label="Guardar"
              />
            <x-button
              icon="bxs.hand"
              class="btn btn-error"
              label="Cancelar"
              wire:click="reset_tarea"
              />
          </div>
        </x-form>
      </x-card>
    </div>
  </div>


  @if ($tema->tareas->count() == 0)
    <x-alert title="No hay tareas registrados" icon="bxs.info-circle" class="alert-warning" />
  @else
    <x-card class="text-content-primary">
      <x-table
        :headers="$headers"
        :rows="$tema->tareas"
        link="/tarea/{id}"
        striped
        >
        @scope('header_id', $header)
          <span class="text-primary-content">{{ $header['label'] }}</span>
        @endscope
        @scope('header_nombre', $header)
          <span class="text-primary-content">{{ $header['label'] }}</span>
        @endscope

        @scope('actions', $tarea)
          <div class="flex space-x-1">
            <x-button
              icon="bxs.trash"
              class="btn-circle btn-ghost btn-xs text-error"
              tooltip-left="Eliminar"
              wire:click="delete({{ $tarea->id }})"
              />
          </div>
        @endscope
      </x-table>
    </x-card>
  @endif

</div>
