@if ($paginator->hasPages())
    <nav class="pagination-ctrl" data-component-list="Pagination" data-analytics-activitymap-region-id="pagination"
        aria-label="pagination">
        <div class="pagination-ctrl__content">
            @if ($paginator->onFirstPage())
                <a role="button" href=""
                    class="pagination-ctrl__btn icon icon-paginationleft pagination-ctrl__btn--previous pagination-ctrl__btn--disabled"
                    aria-disabled="true" aria-label="@lang('pagination.previous')" data-analytics-title="@lang('pagination.previous')"
                    previewlistener="true"><i class="fa-solid fa-angle-left"></i></a>
            @else
                <a role="button" href="{{ $paginator->previousPageUrl() }}"
                    class="pagination-ctrl__btn icon icon-paginationleft pagination-ctrl__btn--previous"
                    aria-disabled="false" rel="prev" aria-label="@lang('pagination.previous')"
                    data-analytics-title="@lang('pagination.previous')" previewlistener="true"><i
                        class="fa-solid fa-angle-left"></i></a>
            @endif


            <div class="pagination-ctrl__info">
                <span class="visuallyhidden" id="pagination-label" aria-hidden="true">Page Number
                    {{ $paginator->currentPage() }}</span>
                <input aria-label="Page Number" class="input" id="pagination-input" type="text"
                    value="{{ $paginator->currentPage() }}">
                <p class="pagination-text" role="text">
                    <span>of</span>
                    <span class="pagination-total">{{ $paginator->lastPage() }}</span>
                </p>
            </div>

            @if ($paginator->hasMorePages())
                <a role="button" href="{{ $paginator->nextPageUrl() }}"
                    class="pagination-ctrl__btn icon icon-paginationright pagination-ctrl__btn--next" rel="next"
                    aria-label="@lang('pagination.next')" data-analytics-title="@lang('pagination.next')" previewlistener="true"><i
                        class="fa-solid fa-angle-right"></i></a>
            @else
                <a role="button" href="#"
                    class="pagination-ctrl__btn icon icon-paginationright pagination-ctrl__btn--next pagination-ctrl__btn--disabled"
                    aria-label="@lang('pagination.next')" data-analytics-title="@lang('pagination.next')" previewlistener="true"
                    aria-disabled="true" tabindex="-1"><i class="fa-solid fa-angle-right"></i></a>
            @endif
        </div>
    </nav>
@endif
@push('js')
    <script type="text/javascript" defer>
        var lastPage = {{ $paginator->lastPage() }};
        var currentPage = {{ $paginator->currentPage() }};
    </script>
    <script src="{{ versionResource('assets/js/support/unified.js') }}" defer></script>
@endpush