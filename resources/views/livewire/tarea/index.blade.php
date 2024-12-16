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
    $tareas = Tarea::where('tarea_padre_id', $parentId)->get();
    $result = [];

    foreach ($tareas as $tarea) {
      $tarea->nivel = $nivel;
      $tarea->nombre_proyecto = $tarea->tema->proyecto->nombre;
      $tarea->nombre_tema = $tarea->tema->nombre;

      $tarea->nombre_tarea = "";
      for ($i=0; $i<$nivel; $i++) {
        $tarea->nombre_tarea .= "➡️";
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

  <x-card>
    <x-slot:header>
      <h2 class="card-title">Tareas</h2>
    </x-slot>

    <div class="overflow-x-auto">
      <table class="relative table table-auto">
        <thead class="text-sm font-bold tracking-wider uppercase text-primary-content">
          <tr>
            <th class="sticky w-1 text-center">ID</th>
            <th class="w-96">Tarea</th>
            <th>Acciones<br />Realizadas</th>
            <th class="w-32"></th>
          </tr>
        </thead>
        <tbody>
          @foreach ($tareas as $tarea)
            <tr class="hover:bg-neutral/30">
              <td class="text-center">{{ $tarea->id }}</td>

              {{-- Info de la tarea --}}
              <td class="space-y-1">
                <div class="text-lg">{{ $tarea->nombre_proyecto }}</div>
                <div>{{ $tarea->nombre_tema }}</div>
                <div class="flex items-start space-x-1 font-bold text-accent">
                  {{-- estado == en proceso, indicador verde --}}
                  <div class="flex-shrink-0 w-3 h-3 mt-1 rounded-full bg-success"></div>
                  <div>
                    {{ $tarea->nombre_tarea }}
                  </div>
                </div>
                <div class="flex justify-between">
                  <div class="flex items-center space-x-1">
                    <x-icon name="bxs.user" class="w-3 h-3" />
                    <div>{{ $tarea->asignadaA->name }}</div>
                  </div>
                  <div class="flex items-center space-x-1">
                    <x-icon name="bxs.calendar" class="w-3 h-3" />
                    <div>{{ $tarea->fecha_compromiso }}</div>
                  </div>
                </div>
                <x-badge
                  :value="$tarea->tipo"
                  class="badge-info"
                  />
              </td>

              {{-- Acciones --}}
              <td>
                @if ($tarea->acciones->count() > 0)
                  @foreach ($tarea->acciones as $accion)
                    <div class="my-2">
                      <div class="flex space-x-1">
                        <x-icon name="bxs.calendar" class="w-3 h-3" />
                        <div>{{ $accion->fecha }}</div>
                      </div>
                      <div>{{ $accion->descripcion }}</div>
                    </div>
                  @endforeach
                @else
                  <x-alert
                    title="Sin acciones"
                    message="No se han registrado acciones para esta tarea"
                    class="alert-warning"
                    icon="bxs.time"
                    />
                @endif
                <x-button
                  icon="bxs.plus-circle"
                  label="Agregar Acción"
                  class="mt-2 btn-primary btn-xs"
                  wire:click='n({{ $tarea->id }})'
                  />
              </td>

              <td>
                <x-button icon="o-eye" class="btn-circle btn-ghost btn-xs" tooltip-left="ver" />
                <x-button icon="o-pencil" class="btn-circle btn-ghost btn-xs" tooltip-left="editar" />
                <x-button icon="o-trash" class="btn-circle btn-ghost btn-xs" tooltip-left="eliminar" />
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </x-card>
</div>
