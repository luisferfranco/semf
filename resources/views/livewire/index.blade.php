<?php

use App\Models\Tema;
use App\Models\User;
use App\Models\Tarea;
use App\Models\Proyecto;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Validate;

new class extends Component {
  public $proyecto = null;
  public $tema = null;
  public $users;
  public $tipos;

  // Tarea que se va a crear o editar
  public $tarea;
  public $tareaPadre = null;

  // Tareas y encabezado de tabla
  public $tareas = null;
  public $headers = null;

  // Modal y formulario
  public $mostrarModal = false;
  public $nombre = null;
  public $asignada_a = null;
  public $fecha_compromiso = null;
  public $descripcion = null;
  public $tipo = null;

  public function mount() {
    $this->users = User::orderBy('name')->get();
    $this->tipos = [
      ['id' => 'administrativo', 'name' => 'Administrativo'],
      ['id' => 'operativo', 'name' => 'Operativo'],
    ];

    // Tareas
    $this->cargaTareas();
    $this->headers = [
      ['key' => 'id', 'label' => '#'],
      ['key' => 'nombre', 'label' => 'Nombre'],
      ['key' => 'asignada_a', 'label' => 'Asignada a'],
      ['key' => 'fecha_compromiso', 'label' => 'Fecha compromiso', 'format' => ['date', 'd/m/Y']],
      ['key' => 'tipo', 'label' => 'Tipo'],
    ];

  }

  #[On('proyecto-actualizado')]
  public function actualizarProyecto($value) {
    $this->proyecto = Proyecto::find($value);
    $this->tema = null;
    $this->tareas = null;
    $this->cargaTareas();
  }

  #[On('tema-actualizado')]
  public function actualizarTema($value) {
    $this->tema = Tema::find($value);
    $this->tareas = null;
    $this->cargaTareas();
  }

  public function cargaTareas($parentId = null, $level = 0) {
    if ($level === 0) {
      if ($this->proyecto) {
        $tareas = Tarea::where('proyecto_id', $this->proyecto->id)
                       ->whereNull('tarea_padre_id')
                       ->get();
      } elseif ($this->tema) {
        $tareas = Tarea::where('tema_id', $this->tema->id)
                       ->whereNull('tarea_padre_id')
                       ->get();
      } else {
        $tareas = Tarea::whereNull('tarea_padre_id')->get();
      }
    } else {
      $tareas = Tarea::where('tarea_padre_id', $parentId)->get();
    }

    foreach ($tareas as $tarea) {
      $tarea->level = $level;
      $this->tareas[] = $tarea;
      $this->cargaTareas($tarea->id, $level + 1);
    }
  }

  public function nuevaTarea($padre = null) {
    $this->mostrarModal = true;
    $this->tareaPadre = $padre ? Tarea::find($padre) : null;

    $this->tarea = new Tarea();
    $this->tareas = null;
    $this->cargaTareas();
  }

  public function guardar() {
    $this->validate([
      'nombre' => 'required',
    ]);

    $this->tarea->nombre = $this->nombre;
    $this->tarea->tarea_padre_id = $this->tareaPadre->id ?? null;
    $this->tarea->descripcion = $this->descripcion;
    $this->tarea->asignada_a = $this->asignada_a;
    $this->tarea->fecha_compromiso = $this->fecha_compromiso;
    $this->tarea->tipo = $this->tipo ?? 'administrativo';
    $this->tarea->proyecto_id = $this->proyecto->id ?? null;
    $this->tarea->tema_id = $this->tema->id ?? null;
    $this->tarea->asignada_por = auth()->id();
    $this->tarea->fecha_inicio = now();
    $this->tarea->save();

    $this->resetVars();

    $this->tareas = null;
    $this->cargaTareas();
    $this->mostrarModal = false;
  }

  public function resetVars() {
    $this->nombre = "";
    $this->descripcion = "";
    $this->asignada_a = null;
    $this->fecha_compromiso = null;
    $this->tipo = null;
  }

  public function hideModal() {
    $this->resetVars();
    $this->mostrarModal = false;
  }

  public function editarTarea($id) {
    $this->tareas = null;
    $this->cargaTareas();

    $this->tarea = Tarea::find($id);
    $this->tareaPadre = $this->tarea->tarea_padre_id ? Tarea::find($this->tarea->tarea_padre_id) : null;
    $this->nombre = $this->tarea->nombre;
    $this->descripcion = $this->tarea->descripcion;
    $this->asignada_a = $this->tarea->asignada_a;
    $this->fecha_compromiso = $this->tarea->fecha_compromiso;
    $this->tipo = $this->tarea->tipo;
    $this->mostrarModal = true;
  }
}; ?>

<div>
  {{-- Modal para crear la tarea --}}
  <x-modal wire:model='mostrarModal'>
    <x-slot name="title">Nueva Tarea</x-slot>
    <form wire:submit.prevent='guardar'>
      @if ($tareaPadre)
        <p class="text-xs text-neutral-content">Tarea padre: {{ $tareaPadre->nombre }}</p>
      @endif
      <x-input label="Nombre"
                wire:model="nombre"
                placeholder="Nombre de la tarea"
                required
                />
      <x-textarea label="Descripción"
                  wire:model="descripcion"
                  placeholder="Descripción de la tarea"
                  rows="5"
                  />
      <div class="flex items-center justify-between">
        <x-select label="Asignada a"
                  wire:model="asignada_a"
                  :options="$users"
                  />
        <x-select label="Tipo"
                  wire:model="tipo"
                  :options="$tipos"
                  />
        <x-input label="Fecha compromiso"
                  wire:model="fecha_compromiso"
                  type="date"
                  />
      </div>
      <div class="mt-4">
        <x-button label="Guardar"
                  type="submit"
                  icon="bxs.save"
                  class="btn-primary"
                  />
        <x-button label="Cancelar"
                  icon="bxs.x-circle"
                  class="ml-1 btn-error"
                  wire:click="hideModal"
                  />
      </div>
    </form>
  </x-modal>

  <x-card class="flex-col justify-start space-y-2 shadow-xl">
    <livewire:selectproyecto />
    <livewire:selecttema />
  </x-card>

  <div class="mt-4">
    @if ($proyecto)
      <h2 class="text-2xl font-bold">{{ $proyecto->nombre }}</h2>
    @endif

    @if ($tema)
      <h3 class="text-xl font-bold">{{ $tema->nombre }}</h3>
    @endif
  </div>

  <x-card class="mt-4">
    <x-button wire:click='nuevaTarea'
              class="mb-4 btn btn-primary"
              label="Nueva Tarea"
              icon="bxs.plus-circle"
              />
    @if ($tareas !== null)
      <x-table  :headers="$headers"
                :rows="$tareas"
                striped
                >
        @scope('cell_id', $tarea)
          <p>{{ $tarea->id }} ({{ $tarea->level }})</p>
        @endscope

        @scope('cell_nombre', $tarea)
          <p class="font-bold" style="margin-left: {{ 24 * $tarea->level }}px;">{{ $tarea->nombre }}</p>
          <p class="text-xs text-neutral-content">{{$tarea->descripcion}}</p>
        @endscope

        @scope('cell_asignada_a', $tarea)
          @if ($tarea->asignadaA)
            <p>{{ $tarea->asignadaA->name }}</p>
          @else
            <p>⚠️</p>
          @endif
        @endscope

        @scope('actions', $tarea)
          <div class="flex space-x-1">
            <x-button icon="bxs.plus-circle"
                      class="btn btn-primary btn-sm"
                      wire:click="nuevaTarea({{ $tarea->id }})"
                      />
            <x-button icon="bxs.edit"
                      class="btn btn-primary btn-sm"
                      wire:click="editarTarea({{ $tarea->id }})"
                      />
          </div>
        @endscope

      </x-table>
    @else
      <p>No hay tareas</p>
    @endif
  </x-card>
</div>
