<?php

use App\Models\Tarea;
use App\Models\Accion;
use Mary\Traits\Toast;
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;

new class extends Component {
  use Toast;

  public $tareas;
  public $accionModal = false;

  #[Rule('required')]
  public $accion;

  public $nuevaTarea;

  public function mount() {
    $this->tareas = $this->getTareasConNivel();
    $this->nuevaTarea = new Tarea();
  }

  private function getTareasConNivel($parentId = null, $nivel = 0) {
    $tareas = Tarea::where('tarea_padre_id', $parentId)
      ->orderBy('tema_id','asc')
      ->get();
    $result = [];

    foreach ($tareas as $tarea) {
      $tarea->nivel = $nivel;
      $tarea->nombre_proyecto = $tarea->tema->proyecto->nombre;
      $tarea->nombre_tema = $tarea->tema->nombre;

      $tarea->nombre_tarea = "";
      for ($i=0; $i<$nivel; $i++) {
        $tarea->nombre_tarea .= "&nbsp;&nbsp;&nbsp;&nbsp;";
      }
      $tarea->nombre_tarea .= $tarea->nombre;

      $result[] = $tarea;


      $r = $this->getTareasConNivel($tarea->id, $nivel + 1);
      if ($r == null) continue;

      $result = array_merge($result, $r);
    }

    return $result;
  }

  public function n($tareaId) {
    $this->tareas = $this->getTareasConNivel();
    $this->nuevaTarea = Tarea::find($tareaId);
    $this->accionModal = true;
  }

  public function cancelaAccion() {
    $this->tareas = $this->getTareasConNivel();
    $this->accionModal = false;
    $this->accion = "";
  }

  public function guardaAccion() {
    $this->tareas = $this->getTareasConNivel();
    $this->validate();

    $a = Accion::create([
      'descripcion' => $this->accion,
      'usuario_id'  => auth()->id(),
      'tarea_id'    => $this->nuevaTarea->id,
      'fecha'       => now(),
    ]);
    info($a);

    $this->accionModal = false;
    $this->accion = "";

    $this->success(
      'Acciones guardadas',
      icon: 's-check'
    );
  }

  public function filtroProyecto($proyectoId) {
    $this->tareas = Tarea::whereHas('tema', function($q) use ($proyectoId) {
      $q->where('proyecto_id', $proyectoId);
    })->get();
    info($this->tareas);
  }
}; ?>

<div>
  {{-- Modal para crear/editar acciones --}}
  <x-modal wire:model="accionModal" class="backdrop-blur">
    <div class="my-6 font-bold text-primary">{{ $nuevaTarea->nombre }}</div>

    <x-form wire:submit='guardaAccion'>
      <x-textarea
        wire:model='accion'
        rows="5"
        placeholder="Escribe aquí las acciones que se realizaron"
        hint="Usa Markdown"
        />

      <div class="flex space-x-1">
        <x-button
          label="Guardar"
          class="btn btn-primary"
          type="submit"
          />
        <x-button
          label="Cancel"
          class="btn btn-error"
          wire:click='cancelaAccion'
          />
      </div>
    </x-form>
  </x-modal>

  <x-card class="mb-12" title="Tareas">
    @php
      $proyecto = "";
      $tema = "";
    @endphp

    @foreach ($tareas as $tarea)
      @if ($proyecto != $tarea->nombre_proyecto)
        @php
          $proyecto = $tarea->nombre_proyecto;
        @endphp

        <div
          class="px-2 mt-4 text-xl font-bold"
          wire:click='filtroProyecto({{ $tarea->tema->proyecto_id }})'
          >
          {{ $proyecto }}
        </div>
      @endif

      @if ($tema != $tarea->nombre_tema)
        @php
          $tema = $tarea->nombre_tema;
        @endphp

        <div class="px-2 text-lg font-bold">{{ $tema }}</div>
      @endif

      <div class="flex items-start w-full px-2 py-1 space-x-6 rounded-lg hover:bg-neutral/30">

        <div class="flex flex-col flex-grow">
          <div class="text-lg">
            {!! $tarea->nombre_tarea !!}
            <x-badge
              :value="$tarea->tipo"
              class="badge-info"
              />
          </div>
          <div class="flex items-center space-x-1 text-neutral">
            <x-icon name="bxs.user" class="w-3 h-3" />
            <div>{{ $tarea->asignadaA->name }}</div>
          </div>
          <div class="flex items-center space-x-1 text-neutral">
            <x-icon name="bxs.calendar" class="w-3 h-3" />
            <div>{{ $tarea->fecha_compromiso }}</div>
          </div>
          <div>
            @if ($tarea->acciones->count() > 0)
              <div x-data="{ open: false }">
                <div x-show="!open">
                  <div class="cursor-pointer" @click="open=true">
                    {{ $tarea->acciones->count() }} acciones
                    <x-icon
                      name="bxs.chevron-down"
                      class="w-4 h-4"
                      />
                  </div>
                </div>

                <div x-show="open">
                  <div
                    class="flex items-center space-x-1 cursor-pointer"
                    @click="open = false"
                    >
                    <div>Cerrar</div>
                    <x-icon
                      name="bxs.chevron-up"
                      class="w-4 h-4 cursor-pointer"
                      />
                  </div>
                  @foreach ($tarea->acciones as $accion)
                    <div class="my-2">
                      <div class="flex items-center space-x-1 text-sm text-neutral">
                        <x-icon name="bxs.calendar" class="w-3 h-3" />
                        <div>{{ $accion->fecha }}</div>
                      </div>
                      <div>{{ $accion->descripcion }}</div>
                    </div>
                  @endforeach
                </div>
              </div>
            @else
              <div class="flex items-center space-x-1 text-neutral">
                <x-icon name="bxs.time" class="w-4 h-4" />
                <div>No se han registrado acciones para esta tarea</div>
              </div>
            @endif
            <x-button
              icon="bxs.plus-circle"
              label="Agregar Acción"
              class="mt-2 btn-success btn-sm"
              wire:click='n({{ $tarea->id }})'
              />
          </div>
        </div>



      </div>

    @endforeach
  </x-card>

</div>
