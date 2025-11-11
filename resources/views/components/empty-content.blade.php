<div class="flex flex-col items-center justify-center p-6 text-center text-gray-500 bg-white">
    <img class="emptyContent mb-6 w-[220px] md:w-[450px]" src="{{ asset('img/logo/statement-Byond-Black.webp') }}" alt="Empty Content Logo">

    {{-- Title Slot (optional) --}}
    @isset($title)
        <h3 class="text-xl font-semibold text-gray-700 mb-2">
            {{ $title }}
        </h3>
    @endisset

    {{-- Default Slot for Message --}}
    <p class="text-base leading-relaxed">
        {{ $slot }}
    </p>

    {{-- Optional Action Button Slot (if needed) --}}
    @if (isset($action))
        <div class="mt-4">
            {{ $action }}
        </div>
    @endif
</div>