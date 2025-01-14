<?php

use App\Models\User;
use Livewire\Volt\Component;

new class extends Component {
  public $user;
  public $misTareas;
  public $tareas;

  public function mount($id = null) {
    $this->user = ($id === null)
      ? auth()->user()
      : User::find($id);

    $this->misTareas = $this->user->tareasParaMi;
    $this->tareas = $this->user->tareas;
  }
}; ?>

<div>
  <x-card>
    <x-h1>{{ $user->name }}</x-h1>

    <section class="mt-4">
      <p class="text-xl font-bold">Tareas para mi</p>
      <ul>
        @foreach ($misTareas as $tarea)
          <li>
            <x-button class="btn-ghost btn-sm"
              link="{{ route('tarea.show', $tarea) }}"
              >
              {{ $tarea->id }} {{ $tarea->nombre }}
            </x-button>
          </li>
        @endforeach
      </ul>
    </section>

    <section class="mt-4">
      <p class="text-xl font-bold">Tareas creadas por mi</p>
      <ul>
        @foreach ($tareas as $tarea)
          <li>
            <x-button class="btn-ghost btn-sm"
              link="{{ route('tarea.show', $tarea) }}"
              >
              {{ $tarea->id }} {{ $tarea->nombre }}
            </x-button>
          </li>
        @endforeach
      </ul>
    </section>
  </x-card>
</div>
