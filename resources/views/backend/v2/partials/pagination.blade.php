@if ($paginator->hasPages())
    <div
        class="flex flex-col sm:flex-row items-center justify-between gap-3 px-5 py-4 border-t border-gray-100 dark:border-gray-800">
        <p class="text-xs text-gray-500 dark:text-gray-400">
            Showing <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $paginator->firstItem() }}</span>
            to <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $paginator->lastItem() }}</span>
            of <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $paginator->total() }}</span> results
        </p>

        <div class="flex items-center gap-2">
            @if ($paginator->onFirstPage())
                <span
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-300 dark:text-gray-600 cursor-not-allowed">
                    <i class="fa-solid fa-chevron-left text-[10px]"></i> Previous
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all">
                    <i class="fa-solid fa-chevron-left text-[10px]"></i> Previous
                </a>
            @endif

            <span
                class="px-3 py-2 rounded-xl bg-primary-50 dark:bg-primary-950/40 text-xs font-semibold text-primary-700 dark:text-primary-400">
                {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all">
                    Next <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            @else
                <span
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-300 dark:text-gray-600 cursor-not-allowed">
                    Next <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </span>
            @endif
        </div>
    </div>
@endif
