<!DOCTYPE html>
<html lang="en" x-data="adminShell()" x-init="init()">

<head>
    <script>
        (function() {
            var darkMode = localStorage.getItem('darkMode');
            if (darkMode === 'true' || (darkMode === null && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') || {{ $setting->system_name ?? config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @if (!empty($setting->favicon))
        <link rel="icon" type="image/png" href="{{ asset($setting->favicon) }}">
    @endif

    @vite(['resources/css/admin.css', 'resources/js/admin.js'])

    <link rel="stylesheet" href="{{ asset('backend/vendor/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/vendor/toastr/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/vendor/datatables/dataTables.min.css') }}">

    <script>
        function adminShell() {
            return {
                sidebarOpen: localStorage.getItem('sidebarCollapsed') !== 'true',
                sidebarHover: false,
                darkMode: localStorage.getItem('darkMode') === 'true' ?
                    true : (localStorage.getItem('darkMode') === 'false' ? false : window.matchMedia(
                        '(prefers-color-scheme: dark)').matches),
                mobileSidebarOpen: false,
                profileDropdownOpen: false,
                init() {
                    this.$watch('darkMode', val => {
                        localStorage.setItem('darkMode', val);
                        val ? document.documentElement.classList.add('dark') : document.documentElement
                            .classList.remove('dark');
                    });
                    if (this.darkMode) document.documentElement.classList.add('dark');
                    if (window.innerWidth < 1024) this.sidebarOpen = false;
                    window.addEventListener('resize', () => {
                        window.innerWidth < 1024 ? this.sidebarOpen = false : this.sidebarOpen = true;
                    });
                },
                toggleSidebar() {
                    this.sidebarOpen = !this.sidebarOpen;
                    localStorage.setItem('sidebarCollapsed', !this.sidebarOpen);
                },
                toggleDarkMode() {
                    this.darkMode = !this.darkMode;
                    window.dispatchEvent(new CustomEvent('darkModeChange'));
                },
                isSidebarExpanded() {
                    return this.sidebarOpen || this.sidebarHover;
                }
            }
        }

        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }
    </script>
    @stack('style')
    @stack('styles')
</head>

<body
    class="font-sans antialiased bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 overflow-hidden">

    <!-- Mobile sidebar overlay -->
    <div x-show="mobileSidebarOpen" x-cloak x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" @click="mobileSidebarOpen = false"
        class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm lg:hidden"></div>

    @include('backend.v2.partials.sidebar')

    <div class="transition-all duration-300" x-cloak
        :class="isSidebarExpanded() ? 'lg:ml-[260px]' : 'lg:ml-[72px]'">

        @include('backend.v2.partials.header')

        <main
            class="h-[calc(100vh-4rem)] overflow-y-auto p-4 sm:p-6 lg:p-8 bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100">
            <div class="w-full max-w-screen-2xl mx-auto space-y-6">
                @yield('content')
            </div>
        </main>
    </div>

    @include('backend.v2.partials.scripts')
    @stack('script')
    @stack('scripts')
</body>

</html>
