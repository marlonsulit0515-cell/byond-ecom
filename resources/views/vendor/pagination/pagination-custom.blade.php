@if ($paginator->hasPages())
    <div class="flex items-center justify-center gap-8">
        {{-- Previous Button --}}
        @if ($paginator->onFirstPage())
            <button disabled class="rounded-md border border-[#f4eedf] p-2.5 text-center text-sm transition-all shadow-sm text-[#762c21] opacity-50 pointer-events-none" type="button" aria-label="{{ __('pagination.previous') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                    <path fill-rule="evenodd" d="M11.03 3.97a.75.75 0 0 1 0 1.06l-6.22 6.22H21a.75.75 0 0 1 0 1.5H4.81l6.22 6.22a.75.75 0 1 1-1.06 1.06l-7.5-7.5a.75.75 0 0 1 0-1.06l7.5-7.5a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
                </svg>
            </button>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="rounded-md border border-[#f4eedf] p-2.5 text-center text-sm transition-all shadow-sm hover:shadow-lg text-[#762c21] hover:text-white hover:bg-[#762c21] hover:border-[#762c21] focus:text-white focus:bg-[#762c21] focus:border-[#762c21] active:border-[#762c21] active:text-white active:bg-[#762c21]" aria-label="{{ __('pagination.previous') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                    <path fill-rule="evenodd" d="M11.03 3.97a.75.75 0 0 1 0 1.06l-6.22 6.22H21a.75.75 0 0 1 0 1.5H4.81l6.22 6.22a.75.75 0 1 1-1.06 1.06l-7.5-7.5a.75.75 0 0 1 0-1.06l7.5-7.5a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
                </svg>
            </a>
        @endif
        
        {{-- Page Info --}}
        <p class="text-[#762c21]">
            Page <strong class="text-[#762c21] font-semibold">{{ $paginator->currentPage() }}</strong> of&nbsp;<strong class="text-[#762c21] font-semibold">{{ $paginator->lastPage() }}</strong>
        </p>
        
        {{-- Next Button --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="rounded-md border border-[#f4eedf] p-2.5 text-center text-sm transition-all shadow-sm hover:shadow-lg text-[#762c21] hover:text-white hover:bg-[#762c21] hover:border-[#762c21] focus:text-white focus:bg-[#762c21] focus:border-[#762c21] active:border-[#762c21] active:text-white active:bg-[#762c21]" aria-label="{{ __('pagination.next') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                    <path fill-rule="evenodd" d="M12.97 3.97a.75.75 0 0 1 1.06 0l7.5 7.5a.75.75 0 0 1 0 1.06l-7.5 7.5a.75.75 0 1 1-1.06-1.06l6.22-6.22H3a.75.75 0 0 1 0-1.5h16.19l-6.22-6.22a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                </svg>
            </a>
        @else
            <button disabled class="rounded-md border border-[#f4eedf] p-2.5 text-center text-sm transition-all shadow-sm text-[#762c21] opacity-50 pointer-events-none" type="button" aria-label="{{ __('pagination.next') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                    <path fill-rule="evenodd" d="M12.97 3.97a.75.75 0 0 1 1.06 0l7.5 7.5a.75.75 0 0 1 0 1.06l-7.5 7.5a.75.75 0 1 1-1.06-1.06l6.22-6.22H3a.75.75 0 0 1 0-1.5h16.19l-6.22-6.22a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                </svg>
            </button>
        @endif
    </div>
@endif