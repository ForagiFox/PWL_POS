@props([
    'title' => 'NaN',
    'value' => 0,
    'id' => 'wave-' . uniqid(),
    'bg' => 'bg-primary',
    'wavecolor' => 'rgba(255,255,255,0.2)'
])
<div class="{{ $bg }} shadow rounded w-100 position-relative overflow-hidden">
    <svg class="position-absolute top-0 start-0 w-100 h-100" xmlns="http://www.w3.org/2000/svg">
        <defs></defs>
        <path id="{{ $id }}" d=""/>
    </svg>

    <div class="p-2 position-relative">
        <h3 class="text-white text-bold">{{ $title }}</h3>
        <h4 class="text-white mb-4">{{ $value }}</h4>
    </div>
</div>

@push('css')

@endpush


@push('js')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll("path[id^='wave-']").forEach(function (svg) {
            $(svg).wavify({
                height: 40,
                bones: 5,
                amplitude: 40,
                color: '{{ $wavecolor }}', // bisa dibuat dinamis dari props juga
                speed: 0.25
            });
        });
    });
</script>
@endpush


