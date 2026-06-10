@if ($paginator->hasPages())
    <nav style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
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
        <ul class="pagination" style="display: flex; align-items: center; gap: 0.25rem; list-style: none; margin: 0; padding: 0;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="disabled" aria-disabled="true" aria-label="@lang('pagination.previous')" style="display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; font-size: 0.875rem; font-weight: 500; color: #9ca3af; background: #f3f4f6; border: 1px solid #d1d5db; border-radius: 0.25rem; cursor: not-allowed;">
                    <span aria-hidden="true" style="font-size: 0.75rem;">&lsaquo;</span>
                </li>
            @else
                <li style="display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; font-size: 0.875rem; font-weight: 500; color: #374151; background: white; border: 1px solid #d1d5db; border-radius: 0.25rem;">
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')" style="text-decoration: none; color: inherit; font-size: 0.75rem;">&lsaquo;</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="disabled" aria-disabled="true" style="display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; font-size: 0.875rem; font-weight: 500; color: #6b7280;"><span>{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="active" aria-current="page" style="display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; font-size: 0.875rem; font-weight: 500; color: white; background: #2563eb; border: 1px solid #2563eb; border-radius: 0.25rem;"><span>{{ $page }}</span></li>
                        @else
                            <li style="display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; font-size: 0.875rem; font-weight: 500; color: #374151; background: white; border: 1px solid #d1d5db; border-radius: 0.25rem;"><a href="{{ $url }}" style="text-decoration: none; color: inherit;">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li style="display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; font-size: 0.875rem; font-weight: 500; color: #374151; background: white; border: 1px solid #d1d5db; border-radius: 0.25rem;">
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')" style="text-decoration: none; color: inherit; font-size: 0.75rem;">&rsaquo;</a>
                </li>
            @else
                <li class="disabled" aria-disabled="true" aria-label="@lang('pagination.next')" style="display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; font-size: 0.875rem; font-weight: 500; color: #9ca3af; background: #f3f4f6; border: 1px solid #d1d5db; border-radius: 0.25rem; cursor: not-allowed;">
                    <span aria-hidden="true" style="font-size: 0.75rem;">&rsaquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
