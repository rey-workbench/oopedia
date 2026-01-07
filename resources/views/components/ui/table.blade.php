@props(['tableClass' => ''])

<div {{ $attributes->merge(['class' => 'table-responsive p-0']) }}>
    <table class="table align-items-center mb-0 {{ $tableClass }}">
        {{ $slot }}
    </table>
</div>
