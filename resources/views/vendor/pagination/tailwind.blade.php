@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">

        {{-- Results Info --}}
        <div style="font-size: 0.875rem; color: #374151;">
            {!! __('Showing') !!}
            @if ($paginator->firstItem())
                <span style="font-weight: 600;">{{ $paginator->firstItem() }}</span>
                {!! __('to') !!}
                <span style="font-weight: 600;">{{ $paginator->lastItem() }}</span>
            @else
                {{ $paginator->count() }}
            @endif
            {!! __('of') !!}
            <span style="font-weight: 600;">{{ $paginator->total() }}</span>
            {!! __('results') !!}
        </div>

        {{-- Pagination Links --}}
        <div style="display: flex; align-items: center; gap: 0.25rem;">

            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span style="display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; font-size: 0.875rem; font-weight: 500; color: #9ca3af; background: #f3f4f6; border: 1px solid #d1d5db; border-radius: 0.25rem; cursor: not-allowed;" aria-label="{{ __('pagination.previous') }}" aria-disabled="true">
                    <svg style="width: 0.75rem; height: 0.75rem;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" style="display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; font-size: 0.875rem; font-weight: 500; color: #374151; background: white; border: 1px solid #d1d5db; border-radius: 0.25rem; text-decoration: none;" aria-label="{{ __('pagination.previous') }}">
                    <svg style="width: 0.75rem; height: 0.75rem;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; font-size: 0.875rem; font-weight: 500; color: #6b7280;">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" style="display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; font-size: 0.875rem; font-weight: 500; color: white; background: #2563eb; border: 1px solid #2563eb; border-radius: 0.25rem;">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" style="display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; font-size: 0.875rem; font-weight: 500; color: #374151; background: white; border: 1px solid #d1d5db; border-radius: 0.25rem; text-decoration: none;" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" style="display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; font-size: 0.875rem; font-weight: 500; color: #374151; background: white; border: 1px solid #d1d5db; border-radius: 0.25rem; text-decoration: none;" aria-label="{{ __('pagination.next') }}">
                    <svg style="width: 0.75rem; height: 0.75rem;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
            @else
                <span style="display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; font-size: 0.875rem; font-weight: 500; color: #9ca3af; background: #f3f4f6; border: 1px solid #d1d5db; border-radius: 0.25rem; cursor: not-allowed;" aria-label="{{ __('pagination.next') }}" aria-disabled="true">
                    <svg style="width: 0.75rem; height: 0.75rem;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </span>
            @endif

        </div>
    </nav>
@endif
