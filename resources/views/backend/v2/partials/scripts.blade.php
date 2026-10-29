<!--begin: Vendor JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="{{ asset('backend/vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('backend/vendor/toastr/toastr.min.js') }}"></script>
<script src="{{ asset('backend/vendor/datatables/dataTables.min.js') }}"></script>
<!--end: Vendor JS -->

<script>
    // ─── Toastr Options ────────────────────────────────────────────────────────
    if (window.toastr) {
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": 3500,
            "extendedTimeOut": 1000
        };
    }

    @if (Session::has('t-success'))
        toastr.success("{{ Session::get('t-success') }}");
    @endif
    @if (Session::has('t-error'))
        toastr.error("{{ Session::get('t-error') }}");
    @endif
    @if (Session::has('t-info'))
        toastr.info("{{ Session::get('t-info') }}");
    @endif
    @if (Session::has('t-warning'))
        toastr.warning("{{ Session::get('t-warning') }}");
    @endif
    @if (Session::has('success'))
        toastr.success("{{ Session::get('success') }}");
    @endif
    @if (Session::has('error'))
        toastr.error("{{ Session::get('error') }}");
    @endif

    // ─── Custom Table Scrollbar (under the table) ─────────────────────────────
    function setupTableScrollbar(tableId) {
        const wrapper = document.getElementById(tableId + '_wrapper');
        if (!wrapper) return;

        const card = wrapper.closest('.card');
        const scrollEl = wrapper.querySelector('.dt-scroll-inner') || wrapper.querySelector('.table-responsive');
        if (!scrollEl || !card) return;

        if (wrapper.querySelector('.dt-scrollbar-track')) return;

        const track = document.createElement('div');
        track.className = 'dt-scrollbar-track';
        track.setAttribute('role', 'scrollbar');
        track.setAttribute('aria-orientation', 'horizontal');
        track.innerHTML = '<div class="dt-scrollbar-thumb"></div>';
        scrollEl.insertAdjacentElement('afterend', track);

        const thumb = track.querySelector('.dt-scrollbar-thumb');

        function syncThumb() {
            const sw = scrollEl.scrollWidth;
            const cw = scrollEl.clientWidth;

            if (sw <= cw + 2) {
                track.style.display = 'none';
                return;
            }

            track.style.display = 'block';
            const thumbPct = Math.max((cw / sw) * 100, 12);
            const maxLeft  = 100 - thumbPct;
            const leftPct  = ((scrollEl.scrollLeft / (sw - cw)) * maxLeft);

            thumb.style.width = thumbPct + '%';
            thumb.style.left  = leftPct + '%';
        }

        scrollEl.addEventListener('scroll', syncThumb, { passive: true });
        window.addEventListener('resize', syncThumb);

        let drag = false, startX = 0, startScroll = 0;

        thumb.addEventListener('mousedown', e => {
            drag = true; startX = e.clientX; startScroll = scrollEl.scrollLeft;
            thumb.classList.add('dragging');
            e.preventDefault();
        });
        document.addEventListener('mousemove', e => {
            if (!drag) return;
            const dx       = e.clientX - startX;
            const movable  = track.clientWidth - thumb.clientWidth;
            const range    = scrollEl.scrollWidth - scrollEl.clientWidth;
            scrollEl.scrollLeft = startScroll + (dx / movable) * range;
        });
        document.addEventListener('mouseup', () => {
            drag = false;
            thumb.classList.remove('dragging');
        });

        thumb.addEventListener('touchstart', e => {
            drag = true; startX = e.touches[0].clientX; startScroll = scrollEl.scrollLeft;
        }, { passive: true });
        document.addEventListener('touchmove', e => {
            if (!drag) return;
            const dx      = e.touches[0].clientX - startX;
            const movable = track.clientWidth - thumb.clientWidth;
            const range   = scrollEl.scrollWidth - scrollEl.clientWidth;
            scrollEl.scrollLeft = startScroll + (dx / movable) * range;
        }, { passive: true });
        document.addEventListener('touchend', () => { drag = false; });

        track.addEventListener('click', e => {
            if (e.target === thumb) return;
            const rect  = track.getBoundingClientRect();
            const ratio = (e.clientX - rect.left) / rect.width;
            scrollEl.scrollLeft = (scrollEl.scrollWidth - scrollEl.clientWidth) * ratio;
        });

        syncThumb();
    }

    // ─── Global DataTables Defaults (Identical to Bobby McDowell Admin) ─────────
    if (window.jQuery && $.fn && $.fn.dataTable) {
        $.extend(true, $.fn.dataTable.defaults, {
            autoWidth:  false,
            responsive: false,
            scrollX:    false,
            dom: "<'flex flex-col sm:flex-row justify-between items-center mb-4 gap-4'lf>" +
                 "<'w-full overflow-x-auto dt-scroll-inner' t>" +
                 "<'flex flex-col sm:flex-row justify-between items-center mt-4 gap-4'ip>",
            language: {
                lengthMenu:        "Show _MENU_ entries",
                search:            "",
                searchPlaceholder: "Search...",
                paginate: {
                    previous: '<i class="fa-solid fa-chevron-left"></i>',
                    next: '<i class="fa-solid fa-chevron-right"></i>'
                }
            },
            initComplete: function(settings) {
                const tableId = settings.nTable.id;
                if (tableId) {
                    setTimeout(() => setupTableScrollbar(tableId), 50);
                }
            }
        });

        $(document).on('draw.dt', function(e, settings) {
            const wrapper   = document.getElementById(settings.nTable.id + '_wrapper');
            if (!wrapper) return;
            const scrollEl  = wrapper.querySelector('.dt-scroll-inner');
            const track     = wrapper.querySelector('.dt-scrollbar-track');
            if (scrollEl && track) {
                scrollEl.scrollLeft = 0;
                scrollEl.dispatchEvent(new Event('scroll'));
            }
        });
    }
</script>
