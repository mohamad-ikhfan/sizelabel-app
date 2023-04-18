<div class="gap-2 flex flex-nowrap justify-items-center">
    @foreach ($slots as $slot)
        <button type="button" {!! $slot['method'] !!} class="{{ $slot['class'] }}">
            <i class="{{ $slot['icon'] }}"></i>
            @if ($slot['name'] ?? false)
                <span>{{ $slot['name'] }}</span>
            @endif
        </button>
    @endforeach
</div>
