<?php

use App\Models\Tarea;
use App\Models\Accion;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;

new class extends Component {
  public $tarea = null;
  public $acciones = null;
  public $accion = null;

  public $mostrarModal = false;

  #[Validate('required')]
  public $descripcion;

  public function mount($id) {
    $this->tarea = Tarea::findOrFail($id);
    $this->acciones = $this->tarea->acciones;
  }

  public function nuevaAccion() {
    $this->accion = new Accion();
    $this->mostrarModal = true;
  }

  public function guardar() {
    $this->validate();

    $this->accion->descripcion = $this->descripcion;
    $this->accion->tarea_id = $this->tarea->id;
    $this->accion->usuario_id = auth()->id();
    $this->accion->save();

    $this->acciones = $this->tarea->acciones;
    $this->hideModal();
  }

  public function hideModal() {
    $this->mostrarModal = false;
    $this->descripcion = null;
  }

  public function editarAccion($id) {
    $this->accion = Accion::find($id);
    $this->descripcion = $this->accion->descripcion;
    $this->mostrarModal = true;
  }
}; ?>

<div>
  <x-modal wire:model='mostrarModal' class="backdrop-blur">
    <x-slot name="title">Nueva Acción</x-slot>

    <form wire:submit='guardar'>
      <x-textarea
        wire:model="descripcion"
        placeholder="Describe las acciones tomadas"
        hint="Usa Markdown"
        rows="6"
        />

      <div class="flex justify-end space-x-2">
        <x-button
          type="submit"
          class="btn btn-primary" label="Guardar"
          icon="bxs.save"
          />
        <x-button
          wire:click='hideModal'
          class="btn btn-secondary"
          label="Cancelar"
          icon="bxs.x-circle"
          />
      </div>
    </form>
  </x-modal>

  <x-card class="shadow-[0_3px_10px_rgb(0,0,0,0.2)]">
    <x-button class="mb-2 btn btn-ghost btn-xs"
      link="/"
      icon="bxs.left-arrow-circle"
      label="REGRESAR"
      />
    <x-h1>
      {{ $tarea->nombre }}
      <x-button class="btn btn-ghost btn-xs"
        wire:click='editarTarea'
        icon="bxs.edit"
        />
    </x-h1>
    @if ($tarea->proyecto)
      <p class="text-2xl font-bold">{{ $tarea->proyecto->nombre }}</p>
    @endif
    @if ($tarea->tema)
      <p class="text-xl font-bold">{{ $tarea->tema->nombre }}</p>
    @endif
    <div class="flex flex-col justify-between md:flex-row">
      {{-- Asignada a y estado --}}
      <div>
        @if ($tarea->asignada_a)
          <p>Asignada a: {{ $tarea->asignadaA->name }}</p>
        @endif
        @switch ($tarea->estado)
          @case('en proceso')
            <x-badge class="badge-warning" value="En Proceso" />
            @break
          @case('terminada')
            <x-badge class="badge-success" value="Terminada" />
            @break
          @case('cancelada')
            <x-badge class="badge-error" value="Cancelada" />
            @break
        @endswitch
      </div>

      {{-- Fechas creación/compromiso y tipo --}}
      <div class="flex flex-col">
        <p>Fecha de creación: {{ $tarea->created_at }}</p>
        @if ($tarea->fecha_compromiso)
          <p>Fecha compromiso: {{ $tarea->fecha_compromiso }}</p>
        @endif
        @if ($tarea->tipo == "operativa")
          <x-badge class="badge-info" value="Operativa" />
        @else
          <x-badge class="badge-info" value="Administrativa" />
        @endif
      </div>
    </div>
    @if ($tarea->descripcion)
      <div class="mt-2">
        <p>{{ $tarea->descripcion }}</p>
      </div>
    @endif
  </x-card>

  <x-card class="mt-4 shadow-[0_3px_10px_rgb(0,0,0,0.2)]">
    <p class="text-xl font-bold">Acciones</p>
    <x-button wire:click='nuevaAccion'
              class="mb-4 btn btn-primary btn-sm"
              label="NUEVA ACCIÓN"
              icon="bxs.plus-circle"
              />

    @if ($acciones->count())
      @foreach ($acciones as $accion)
        <x-card class="shadow-[0_3px_10px_rgb(0,0,0,0.2)] mb-4">
          <div class="flex justify-between">
            <p class="text-sm text-neutral">{{ $accion->usuario->name }} {{ $accion->created_at }}</p>
            <x-button class="btn btn-ghost btn-xs"
                      wire:click='editarAccion({{ $accion->id }})'
                      icon="bxs.edit"
                      label="Editar"
                      />
          </div>
          <hr>
          <div class="mt-2 markdown">
            {!! Str::of($accion->descripcion)->markdown() !!}
          </div>
        </x-card>
      @endforeach
    @else
      <p>No hay acciones registradas</p>
    @endif
  </x-card>
</div>

