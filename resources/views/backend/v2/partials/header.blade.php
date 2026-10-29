<header role="banner"
    class="sticky top-0 z-30 bg-white/80 dark:bg-gray-950/80 backdrop-blur-xl border-b border-gray-200 dark:border-gray-800 shadow-sm">
    <div class="flex items-center justify-between h-16 px-4 lg:px-6">
        <!-- Left -->
        <div class="flex items-center gap-3">
            <button @click="toggleSidebar()"
                class="hidden lg:flex w-10 h-10 items-center justify-center rounded-xl text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200 transition-all duration-200"
                :title="sidebarOpen ? 'Collapse Sidebar' : 'Expand Sidebar'" :aria-expanded="sidebarOpen">
                <i class="fa-solid fa-bars-staggered text-lg"></i>
            </button>
            <button @click="mobileSidebarOpen = true"
                class="lg:hidden w-10 h-10 flex items-center justify-center rounded-xl text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200"
                aria-label="Open sidebar" :aria-expanded="mobileSidebarOpen">
                <i class="fa-solid fa-bars text-lg"></i>
            </button>
        </div>

        <!-- Right -->
        <div class="flex items-center gap-1 sm:gap-2">
            <button onclick="toggleFullscreen()"
                class="hidden sm:flex w-10 h-10 items-center justify-center rounded-xl text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200"
                title="Fullscreen">
                <i class="fa-solid fa-expand text-lg"></i>
            </button>

            <button @click="toggleDarkMode()"
                class="w-10 h-10 flex items-center justify-center rounded-xl text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200"
                :title="darkMode ? 'Light Mode' : 'Dark Mode'">
                <i x-show="!darkMode" class="fa-solid fa-moon text-lg"></i>
                <i x-show="darkMode" x-cloak class="fa-solid fa-sun text-lg text-yellow-400"></i>
            </button>

            <!-- Profile dropdown -->
            <div class="relative" @click.outside="profileDropdownOpen = false">
                <button @click="profileDropdownOpen = !profileDropdownOpen"
                    class="flex items-center gap-2 px-2 py-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200 group">
                    @if (!empty(auth()->user()->avatar))
                        <img src="{{ asset(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}"
                            class="w-8 h-8 rounded-full object-cover shadow-md ring-2 ring-white dark:ring-gray-950 flex-shrink-0">
                    @else
                        <div
                            class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-sm font-bold shadow-md ring-2 ring-white dark:ring-gray-950 flex-shrink-0">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                        </div>
                    @endif

                    <div class="hidden lg:block text-left">
                        <p class="text-sm font-semibold text-gray-800 dark:text-white leading-tight">
                            {{ auth()->user()->name ?? 'Admin' }}
                        </p>
                        <p class="text-[10px] text-gray-400 dark:text-gray-500 leading-tight">
                            {{ ucfirst(auth()->user()->role ?? 'Admin') }}
                        </p>
                    </div>
                    <i
                        class="hidden lg:block fa-solid fa-chevron-down text-[10px] text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors"></i>
                </button>

                <div x-show="profileDropdownOpen" x-cloak @click.outside="profileDropdownOpen = false"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-72 bg-white dark:bg-gray-900 rounded-2xl shadow-dropdown border border-gray-200 dark:border-gray-700 overflow-hidden origin-top-right z-50">

                    <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800">
                        <p class="text-sm font-semibold text-gray-800 dark:text-white truncate">
                            {{ auth()->user()->name ?? 'Admin' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()?->email ?? '' }}</p>
                    </div>

                    <div class="p-1.5 border-t border-gray-100 dark:border-gray-800">
                        <a href="{{ route('admin.profile.setting') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all">
                            <i class="fa-solid fa-user w-5 text-center text-gray-400"></i><span>My Profile</span>
                        </a>
                        <a href="{{ route('admin.system.index') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-all">
                            <i class="fa-solid fa-gear w-5 text-center text-gray-400"></i><span>Settings</span>
                        </a>

                        <hr class="my-1 border-gray-100 dark:border-gray-800">

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
                                <i class="fa-solid fa-arrow-right-from-bracket w-5 text-center"></i><span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
