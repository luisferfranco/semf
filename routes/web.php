<?php
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;

Volt::route('/', 'index');

// Rutas de autenticación login, register y logout
Volt::route('/login', 'login')->name('login');
Volt::route('/register', 'register');
Route::get('/logout', function () {
  auth()->logout();
  request()->session()->invalidate();
  request()->session()->regenerateToken();

  return redirect('/');
});

// Protected routes here
Route::middleware('auth')->group(function () {
  Volt::route('/', 'index');

  Volt::route('/proyecto', 'proyecto.index')
    ->name('proyecto.index');
  Volt::route('proyecto/{proyecto}', 'proyecto.show')
    ->name('proyecto.show');
  Volt::route('tema/{tema}', 'tema.show')
    ->name('tema.show');
  Volt::route('tarea', 'tarea.index')
    ->name('tarea.index');

  // Volt::route('/users', 'users.index');
  // Volt::route('/users/create', 'users.create');
  // Volt::route('/users/{user}/edit', 'users.edit');
  // ... more
});


