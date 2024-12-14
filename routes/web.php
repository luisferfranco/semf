<?php
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;

Volt::route('/', 'users.index');

// Rutas de autenticación login, register y logout
Volt::route('/login', 'login')->name('login');
Volt::route('/register', 'register');
Route::get('/logout', function () {
  auth()->logout();
  request()->session()->invalidate();
  request()->session()->regenerateToken();

  return redirect('/');
});

