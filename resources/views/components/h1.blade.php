@props(['label'])
<h1 class="mb-6 text-3xl font-bold tracking-widest uppercase text-primary">
  {{ $label ?? $slot }}
</h1>
