<aside role="navigation" aria-label="Main navigation" x-cloak
    class="fixed top-0 left-0 z-50 h-full flex flex-col bg-white dark:bg-sidebar-dark border-r border-gray-200 dark:border-gray-800 sidebar-transition shadow-lg"
    :class="{
        'w-[260px]': isSidebarExpanded(),
        'w-[72px]': !isSidebarExpanded(),
        'translate-x-0': mobileSidebarOpen,
        '-translate-x-full lg:translate-x-0': !mobileSidebarOpen
    }"
    @mouseenter="sidebarHover = true" @mouseleave="sidebarHover = false">

    <!-- Mobile close button -->
    <div class="lg:hidden flex items-center justify-end p-3 border-b border-gray-200 dark:border-gray-800">
        <button @click="mobileSidebarOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto overflow-x-hidden py-3 px-3" id="sidebar-menu">
        <ul class="flex flex-col gap-1" id="sideNavbar">

            <!-- Overview -->
            <li class="nav-item" x-show="isSidebarExpanded()" x-cloak x-transition.opacity>
                <div
                    class="px-2.5 pt-1 pb-1 text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 whitespace-nowrap">
                    Overview
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                    href="{{ route('admin.dashboard') }}" :title="!isSidebarExpanded() ? 'Dashboard' : ''">
                    <i class="fa-solid fa-gauge-high nav-icon"></i>
                    <span x-show="isSidebarExpanded()" x-cloak x-transition.opacity
                        class="whitespace-nowrap">Dashboard</span>
                </a>
            </li>


            <!-- User Management -->
            <li class="nav-item pt-2" x-show="isSidebarExpanded()" x-cloak x-transition.opacity>
                <div
                    class="px-2.5 pt-2 pb-1 text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 whitespace-nowrap">
                    User Management
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                    href="{{ route('admin.users.index') }}" :title="!isSidebarExpanded() ? 'Users' : ''">
                    <i class="fa-solid fa-users nav-icon"></i>
                    <span x-show="isSidebarExpanded()" x-cloak x-transition.opacity
                        class="whitespace-nowrap">Users</span>
                </a>
            </li>


            <!-- Overview -->
            <li class="nav-item" x-show="isSidebarExpanded()" x-cloak x-transition.opacity>
                <div
                    class="px-2.5 pt-1 pb-1 text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 whitespace-nowrap">
                    Premium Services
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.premium_code.*') ? 'active' : '' }}"
                    href="{{ route('admin.premium_code.index') }}" :title="!isSidebarExpanded() ? 'Premium Codes' : ''">
                    <i class="fa-solid fa-crown nav-icon"></i>
                    <span x-show="isSidebarExpanded()" x-cloak x-transition.opacity class="whitespace-nowrap">Premium
                        Codes</span>
                </a>
            </li>

            <!-- App Content & CMS -->
            <li class="nav-item pt-2" x-show="isSidebarExpanded()" x-cloak x-transition.opacity>
                <div
                    class="px-2.5 pt-2 pb-1 text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 whitespace-nowrap">
                    App Content &amp; CMS
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dynamic_page.*') ? 'active' : '' }}"
                    href="{{ route('admin.dynamic_page.index') }}" :title="!isSidebarExpanded() ? 'Dynamic Pages' : ''">
                    <i class="fa-solid fa-file-lines nav-icon"></i>
                    <span x-show="isSidebarExpanded()" x-cloak x-transition.opacity class="whitespace-nowrap">Dynamic
                        Pages</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.video_tutorial.*') ? 'active' : '' }}"
                    href="{{ route('admin.video_tutorial.index') }}"
                    :title="!isSidebarExpanded() ? 'Video Tutorials' : ''">
                    <i class="fa-solid fa-circle-play nav-icon"></i>
                    <span x-show="isSidebarExpanded()" x-cloak x-transition.opacity class="whitespace-nowrap">Video
                        Tutorials</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dynamic_gallery.*') ? 'active' : '' }}"
                    href="{{ route('admin.dynamic_gallery.index') }}"
                    :title="!isSidebarExpanded() ? 'Dynamic Images' : ''">
                    <i class="fa-solid fa-images nav-icon"></i>
                    <span x-show="isSidebarExpanded()" x-cloak x-transition.opacity class="whitespace-nowrap">Dynamic
                        Images</span>
                </a>
            </li>


            <!-- System Settings -->
            <li class="nav-item pt-2" x-show="isSidebarExpanded()" x-cloak x-transition.opacity>
                <div
                    class="px-2.5 pt-2 pb-1 text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 whitespace-nowrap">
                    System Settings
                </div>
            </li>

            <li class="nav-item" x-data="{ open: {{ request()->routeIs('admin.system.*') || request()->routeIs('admin.mail.*') || request()->routeIs('admin.integration.*') || request()->routeIs('admin.social.*') || request()->routeIs('admin.profile.*') ? 'true' : 'false' }} }">
                <button @click="isSidebarExpanded() ? (open = !open) : (sidebarOpen = true)"
                    class="nav-link nav-toggle w-full text-left" :title="!isSidebarExpanded() ? 'Settings' : ''">
                    <i class="fa-solid fa-gear nav-icon"></i>
                    <span x-show="isSidebarExpanded()" x-cloak x-transition.opacity
                        class="flex-1 text-left whitespace-nowrap">Settings</span>
                    <i x-show="isSidebarExpanded()" x-cloak x-transition.opacity
                        class="fa-solid fa-chevron-down nav-arrow" :class="open ? 'rotated' : ''"></i>
                </button>
                <div x-show="open && isSidebarExpanded()" x-cloak class="nav-submenu">
                    <ul class="flex flex-col gap-1">
                        <li class="nav-item">
                            <a class="nav-link nav-sub-link {{ request()->routeIs('admin.system.*') ? 'active' : '' }}"
                                href="{{ route('admin.system.index') }}">
                                <i class="fa-solid fa-sliders sub-icon"></i>
                                <span x-show="isSidebarExpanded()" x-cloak x-transition.opacity
                                    class="whitespace-nowrap">System Settings</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-sub-link {{ request()->routeIs('admin.mail.*') ? 'active' : '' }}"
                                href="{{ route('admin.mail.index') }}">
                                <i class="fa-solid fa-envelope sub-icon"></i>
                                <span x-show="isSidebarExpanded()" x-cloak x-transition.opacity
                                    class="whitespace-nowrap">Mail Configuration</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-sub-link {{ request()->routeIs('admin.integration.*') ? 'active' : '' }}"
                                href="{{ route('admin.integration.index') }}">
                                <i class="fa-solid fa-plug sub-icon"></i>
                                <span x-show="isSidebarExpanded()" x-cloak x-transition.opacity
                                    class="whitespace-nowrap">Integrations</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-sub-link {{ request()->routeIs('admin.social.*') ? 'active' : '' }}"
                                href="{{ route('admin.social.index') }}">
                                <i class="fa-solid fa-share-nodes sub-icon"></i>
                                <span x-show="isSidebarExpanded()" x-cloak x-transition.opacity
                                    class="whitespace-nowrap">Social Links</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </nav>
</aside>
