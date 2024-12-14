<?php
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new
#[Layout('components.layouts.empty')]
#[Title('Login')]
class extends Component {
  #[Rule('required|email')]
  public string $email = '';

  #[Rule('required')]
  public string $password = '';

  #[Rule('boolean')]
  public bool $remember = false;

  public function mount()
  {
    // It is logged in
    if (auth()->user()) {
      return redirect('/');
    }
  }

  public function login()
  {
    $credentials = $this->validate();

    if (auth()->attempt($this->only(['email', 'password']), $this->remember)) {
      request()->session()->regenerate();
      return redirect()->intended('/');
    }

    $this->addError('email', 'The provided credentials do not match our records.');
  }
}
?>

<div class="mx-auto mt-20 md:w-96">
  <div class="mb-10">Cool image here</div>

  <x-form wire:submit="login">
    <x-input label="E-mail" wire:model="email" icon="o-envelope" inline />
    <x-input label="Password" wire:model="password" type="password" icon="o-key" inline />
    <x-checkbox
      label="Recuérdame"
      wire:model="remember"
    />

    <x-slot:actions>
      <x-button label="Create an account" class="btn-ghost" link="/register" />
      <x-button label="Login" type="submit" icon="o-paper-airplane" class="btn-primary" spinner="login" />
    </x-slot:actions>

  </x-form>
</div>
