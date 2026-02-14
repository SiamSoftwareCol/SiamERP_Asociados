@php
    use Illuminate\Support\Facades\DB;
    $content = DB::table('content_managers')->latest('created_at')->first();
@endphp

<x-filament-widgets::widget>
    <style>
        .title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .description {
            font-size: 1rem;
            color: #6b7280;
            margin-bottom: 1rem;
            text-align: center;
        }

        .image {
            margin-bottom: 1rem;
            max-height: 500px;
            overflow: hidden;
            object-fit: cover;
            width: 100%;

            img {
                display: flex;
                margin: auto;
            }
        }
    </style>

    @if ($content && $content->is_active)
        <x-filament::section>
            {{-- Widget content --}}
            <h1 class="title">{{ $content->name }}</h1>


            <div class="description">
                <p>{{ $content->description }}</p>
            </div>

            <div class="image">
                <img src="{{ $content->image }}" alt="Eventos">
            </div>

            @if ($content->button)
                <div class="mt-4 text-center">
                    <x-filament::button href="{{ $content->button_link }}" tag="a" target="_blank" color="primary">
                        {{ $content->button_text }}
                    </x-filament::button>
                </div>
            @endif
        </x-filament::section>
    @else
        <x-filament::section>
            {{-- Widget content --}}
            <h1 class="title">No hay eventos programados</h1>
            <div class="description">
                <p>No hay eventos programados para este año.</p>
            </div>
        </x-filament::section>
    @endif
</x-filament-widgets::widget>
