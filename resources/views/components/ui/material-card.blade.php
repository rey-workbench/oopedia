@props([
    'title' => '',
    'image' => null,
    'badge' => 'Tersedia',
    'badgeColor' => 'green',
    'icon' => 'fa-book',
    'meta' => [],
    'stats' => null,
    'description' => null,
    'href' => '#',
])

@php
    $badgeColors = [
        'green' => 'bg-gradient-to-br from-green-500 to-teal-400',
        'blue' => 'bg-gradient-to-br from-blue-500 to-cyan-400',
        'yellow' => 'bg-gradient-to-br from-yellow-500 to-orange-400',
        'red' => 'bg-gradient-to-br from-red-500 to-pink-400',
    ];
    $badgeClass = $badgeColors[$badgeColor] ?? $badgeColors['green'];
@endphp

<div class="h-full">
    <div class="bg-white rounded-2xl shadow-lg transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl h-full flex flex-col relative overflow-hidden border-0 mb-6 group">
        <!-- Badge -->
        @if($badge)
        <div class="absolute top-4 left-4 z-10 {{ $badgeClass }} text-white px-4 py-1.5 rounded-full text-xs font-semibold shadow-lg">
            {{ $badge }}
        </div>
        @endif

        <!-- Image -->
        @if($image)
            <div class="h-48 relative rounded-t-2xl overflow-hidden">
                <img src="{{ $image }}" alt="{{ $title }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                <div class="absolute bottom-0 left-0 w-full h-8 bg-gradient-to-t from-white/90 to-transparent z-[2]"></div>
            </div>
        @else
            <div class="h-48 relative rounded-t-2xl overflow-hidden bg-gradient-to-br from-primary-50 to-primary-100">
                <div class="w-full h-full flex items-center justify-center">
                    <i class="fas fa-book-open text-6xl text-primary-300"></i>
                </div>
                <div class="absolute bottom-0 left-0 w-full h-8 bg-gradient-to-t from-white/90 to-transparent z-[2]"></div>
            </div>
        @endif

        <!-- Icon Badge -->
        <div class="absolute top-[11.25rem] right-5 w-12 h-12 rounded-full bg-primary-600 text-white flex items-center justify-center text-xl z-[3] border-3 border-white shadow-lg transition-all duration-300 group-hover:rotate-[15deg] group-hover:bg-primary-700">
            <i class="fas {{ $icon }}"></i>
        </div>

        <!-- Content -->
        <div class="p-6 pt-6 flex flex-col flex-grow">
            <!-- Title -->
            <h3 class="text-xl font-bold text-primary-600 mb-2.5 leading-snug">
                {{ $title }}
            </h3>

            <!-- Meta Info -->
            @if(count($meta) > 0)
            <div class="flex gap-4 mb-3">
                @foreach($meta as $item)
                <div class="flex items-center text-xs text-gray-600">
                    <i class="fas {{ $item['icon'] }} text-primary-600 mr-1.5"></i>
                    <span>{{ $item['text'] }}</span>
                </div>
                @endforeach
            </div>
            @endif

            <div class="h-px bg-gray-200 my-2.5 mb-4"></div>

            <!-- Stats -->
            @if($stats)
            <div class="mb-4">
                <div class="inline-flex items-center px-3 py-1.5 bg-primary-50 rounded-full text-sm text-primary-600 font-medium gap-2">
                    {{ $stats }}
                </div>
            </div>
            @endif

            <!-- Description -->
            @if($description)
            <p class="text-sm text-gray-600 mb-4 line-clamp-3 flex-grow">
                {{ $description }}
            </p>
            @endif

            <!-- Slot for custom content -->
            {{ $slot }}

            <!-- Action Button -->
            @isset($action)
                <div class="mt-auto pt-4">
                    {{ $action }}
                </div>
            @else
                <div class="mt-auto pt-4">
                    <a href="{{ $href }}" class="block w-full text-center bg-gradient-to-r from-primary-600 to-primary-500 text-white font-semibold py-3 rounded-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-0.5">
                        Lihat Detail
                    </a>
                </div>
            @endisset
        </div>
    </div>
</div>
