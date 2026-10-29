@extends('backend.v2.app')

@section('title', 'Dashboard')

@section('content')
    <!-- Page header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2">
                <span
                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-primary-50 dark:bg-primary-950/50 text-primary-600 dark:text-primary-400">
                    <i class="fa-solid fa-chart-pie text-sm"></i>
                </span>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Overview &amp; Analytics</h1>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Real-time performance metrics and member statistics.
            </p>
        </div>

        <div class="flex items-center flex-wrap gap-2.5">
            <button onclick="window.location.reload()"
                class="inline-flex items-center gap-2 px-3.5 py-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all shadow-sm">
                <i class="fa-solid fa-rotate text-xs text-gray-400"></i>
                <span>Refresh</span>
            </button>
        </div>
    </div>

    <!-- Stat cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        @php
            $cards = [
                [
                    'label' => 'Total Users',
                    'value' => number_format($stats['total_users']),
                    'icon' => 'fa-users',
                    'tone' => 'primary',
                    'meta' => '+' . number_format($stats['new_this_week']) . ' this week',
                ],
                [
                    'label' => 'Premium Members',
                    'value' => number_format($stats['premium_users']),
                    'icon' => 'fa-crown',
                    'tone' => 'amber',
                    'meta' => $stats['total_users'] > 0
                        ? round(($stats['premium_users'] / $stats['total_users']) * 100) . '% of all users'
                        : '0% of all users',
                ],
                [
                    'label' => 'Premium Codes',
                    'value' => number_format($stats['total_codes']),
                    'icon' => 'fa-ticket',
                    'tone' => 'violet',
                    'meta' => number_format($stats['used_codes']) . ' used',
                ],
                [
                    'label' => 'Dynamic Pages',
                    'value' => number_format($stats['dynamic_pages']),
                    'icon' => 'fa-file-lines',
                    'tone' => 'emerald',
                    'meta' => number_format($stats['video_tutorials']) . ' video tutorials',
                ],
            ];

            $tones = [
                'primary' => 'bg-primary-50 text-primary-600 dark:bg-primary-950/50 dark:text-primary-400',
                'amber' => 'bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400',
                'violet' => 'bg-violet-50 text-violet-600 dark:bg-violet-950/50 dark:text-violet-400',
                'emerald' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400',
            ];
        @endphp

        @foreach ($cards as $card)
            <div class="stat-card card p-5 hover:shadow-card-hover">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                            {{ $card['label'] }}
                        </p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $card['value'] }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $card['meta'] }}</p>
                    </div>
                    <span
                        class="inline-flex items-center justify-center w-11 h-11 rounded-xl {{ $tones[$card['tone']] }}">
                        <i class="fa-solid {{ $card['icon'] }}"></i>
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Chart + content overview -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
        <div class="card p-5 xl:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">New Registrations</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Last 7 days</p>
                </div>
                <span class="badge bg-primary-50 text-primary-700 dark:bg-primary-950/50 dark:text-primary-400">
                    <i class="fa-solid fa-user-plus"></i> {{ number_format(array_sum($chartData)) }} total
                </span>
            </div>
            <div class="h-[280px]">
                <canvas id="registrationsChart"></canvas>
            </div>
        </div>

        <div class="card p-5">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Content Overview</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">CMS inventory at a glance</p>

            <div class="flex items-center justify-center mb-4">
                <div class="relative" style="width: 180px; height: 180px;">
                    <canvas id="contentDonutChart" width="180" height="180"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($stats['dynamic_pages'] + $stats['video_tutorials'] + $stats['galleries'] + $stats['total_codes']) }}
                        </span>
                        <span class="text-[11px] text-gray-500 dark:text-gray-400">Total Items</span>
                    </div>
                </div>
            </div>

            @php
                $contentItems = [
                    ['label' => 'Dynamic Pages', 'value' => $stats['dynamic_pages'], 'color' => '#6366f1'],
                    ['label' => 'Video Tutorials', 'value' => $stats['video_tutorials'], 'color' => '#8b5cf6'],
                    ['label' => 'Dynamic Images', 'value' => $stats['galleries'], 'color' => '#10b981'],
                    ['label' => 'Premium Codes', 'value' => $stats['total_codes'], 'color' => '#f59e0b'],
                ];
            @endphp

            <div class="grid grid-cols-2 gap-3">
                @foreach ($contentItems as $item)
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: {{ $item['color'] }}"></span>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $item['label'] }}</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($item['value']) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent users -->
    <div class="card overflow-hidden">
        <div class="flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-800">
            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Recent Users</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Latest members who joined</p>
            </div>
            <a href="{{ route('admin.users.index') }}"
                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-950/40 transition-all">
                View all <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">User</th>
                        <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Email</th>
                        <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Plan</th>
                        <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Joined</th>
                        <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($recentUsers as $user)
                        @php
                            $avatarUrl = $user->avatar
                                ? (str_starts_with($user->avatar, 'http') ? $user->avatar : asset($user->avatar))
                                : null;

                            $userPayload = [
                                'id'                 => $user->id,
                                'user_id'            => $user->user_id ?: ('USR-' . $user->id),
                                'name'               => $user->name ?: 'N/A',
                                'email'              => $user->email,
                                'avatar'             => $avatarUrl,
                                'role'               => $user->role ?: 'User',
                                'status'             => $user->status ?: 'active',
                                'gender'             => $user->gender ?: 'N/A',
                                'background'         => $user->background ?: 'N/A',
                                'is_18_plus'         => $user->is_18_plus ? 'Yes' : 'No',
                                'provider'           => $user->provider ?: 'Direct',
                                'plan'               => $user->isPremium() ? 'Premium' : 'Free',
                                'premium_expires_at' => $user->premium_expires_at ? $user->premium_expires_at->format('M d, Y') : ($user->isPremium() ? 'Lifetime' : 'N/A'),
                                'email_verified_at'  => $user->email_verified_at ? $user->email_verified_at->format('M d, Y') : 'No',
                                'created_at'         => $user->created_at ? ($user->created_at->format('d-m-Y') . ' (' . $user->created_at->diffForHumans() . ')') : 'N/A',
                            ];
                        @endphp
                        <tr id="recent-user-row-{{ $user->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                            <td class="table-cell">
                                <div class="flex items-center gap-3">
                                    @if (!empty($user->avatar))
                                        <img src="{{ asset($user->avatar) }}" alt="{{ $user->name }}"
                                            class="w-9 h-9 rounded-full object-cover">
                                    @else
                                        <span
                                            class="w-9 h-9 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 text-white text-xs font-bold flex items-center justify-center">
                                            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                        </span>
                                    @endif
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                            {{ $user->name ?? 'Unnamed' }}</p>
                                        <p class="text-[11px] text-gray-400">{{ $user->user_id ?? '—' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="table-cell">{{ $user->email ?? '—' }}</td>
                            <td class="table-cell">
                                @if ($user->isPremium())
                                    <span class="badge bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-400">
                                        <i class="fa-solid fa-crown text-[9px]"></i> Premium
                                    </span>
                                @else
                                    <span class="badge bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">Free</span>
                                @endif
                            </td>
                            <td class="table-cell whitespace-nowrap"
                                title="{{ optional($user->created_at)->format('d-m-Y h:i A') }}">
                                @if ($user->created_at)
                                    <span class="block text-xs font-medium text-gray-800 dark:text-gray-200">
                                        {{ $user->created_at->format('d-m-Y') }}
                                    </span>
                                    <span class="block text-[11px] text-gray-400 dark:text-gray-500">
                                        {{ $user->created_at->diffForHumans() }}
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="table-cell text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button"
                                        data-user="{{ json_encode($userPayload) }}"
                                        onclick="showUser(this)"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg text-blue-600 bg-blue-100 hover:bg-blue-600 hover:text-white dark:text-blue-400 dark:bg-blue-900/30 dark:hover:bg-blue-600 dark:hover:text-white transition-all shadow-sm hover:shadow"
                                        title="View Details">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </button>
                                    <button type="button"
                                        onclick="deleteUser(event, {{ $user->id }})"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg text-red-600 bg-red-100 hover:bg-red-600 hover:text-white dark:text-red-400 dark:bg-red-900/30 dark:hover:bg-red-600 dark:hover:text-white transition-all shadow-sm hover:shadow"
                                        title="Delete User">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-400 dark:text-gray-500">
                                No users yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!--begin: User Details Modal-->
    <div id="userModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="um_heading">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity" onclick="closeUserModal()"></div>
        <div class="relative flex items-center justify-center min-h-screen p-4 pointer-events-none">
            <div class="w-full max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden pointer-events-auto">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                    <h3 id="um_heading" class="text-base font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                        <i class="fa-solid fa-user text-primary-500"></i>
                        <span>User Details</span>
                    </h3>
                    <button type="button" onclick="closeUserModal()"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all"
                        title="Close">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <div class="px-6 py-5 space-y-5 max-h-[70vh] overflow-y-auto">
                    <div class="flex items-center gap-4 pb-4 border-b border-gray-100 dark:border-gray-700">
                        <div id="um_avatar_container" class="relative">
                            <img id="um_avatar" src="" alt="Avatar"
                                class="w-16 h-16 rounded-full object-cover border-2 border-primary-500/20">
                            <div id="um_avatar_placeholder"
                                class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400">
                                <i class="fa-solid fa-user text-2xl"></i>
                            </div>
                        </div>
                        <div>
                            <h4 id="um_name" class="text-lg font-bold text-gray-800 dark:text-gray-100">-</h4>
                            <p id="um_email" class="text-xs text-gray-500 dark:text-gray-400">-</p>
                            <div class="flex items-center gap-2 mt-1.5">
                                <span id="um_role"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-50 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300">-</span>
                                <span id="um_status"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">-</span>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">User ID</label>
                            <p id="um_user_id" class="text-sm font-medium text-gray-700 dark:text-gray-300">-</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Subscription Plan</label>
                            <p id="um_plan" class="text-sm font-medium text-gray-700 dark:text-gray-300">-</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Plan Expiry</label>
                            <p id="um_plan_expiry" class="text-sm font-medium text-gray-700 dark:text-gray-300">-</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Email Verified</label>
                            <p id="um_email_verified" class="text-sm font-medium text-gray-700 dark:text-gray-300">-</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Gender</label>
                            <p id="um_gender" class="text-sm font-medium text-gray-700 dark:text-gray-300">-</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Background</label>
                            <p id="um_background" class="text-sm font-medium text-gray-700 dark:text-gray-300">-</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">18+ Confirmed</label>
                            <p id="um_18_plus" class="text-sm font-medium text-gray-700 dark:text-gray-300">-</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Auth Provider</label>
                            <p id="um_provider" class="text-sm font-medium text-gray-700 dark:text-gray-300">-</p>
                        </div>
                    </div>
                    <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                        <label class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Joined Date</label>
                        <p id="um_created_at" class="text-sm text-gray-700 dark:text-gray-300">-</p>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                    <button type="button" onclick="closeUserModal()" class="btn-secondary">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!--end: User Details Modal-->
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function() {
            const labels = @json($chartLabels);
            const data = @json($chartData);

            function palette() {
                const dark = document.documentElement.classList.contains('dark');
                return {
                    grid: dark ? 'rgba(148,163,184,0.15)' : 'rgba(148,163,184,0.25)',
                    tick: dark ? '#94a3b8' : '#64748b'
                };
            }

            function build() {
                const ctx = document.getElementById('registrationsChart');
                if (!ctx) return;
                const colors = palette();

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Registrations',
                            data: data,
                            borderColor: '#00A3FF',
                            backgroundColor: (context) => {
                                const chart = context.chart;
                                const {
                                    ctx,
                                    chartArea
                                } = chart;
                                if (!chartArea) return 'rgba(0,163,255,0.15)';
                                const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                                gradient.addColorStop(0, 'rgba(0,163,255,0.35)');
                                gradient.addColorStop(1, 'rgba(0,163,255,0)');
                                return gradient;
                            },
                            fill: true,
                            tension: 0.4,
                            borderWidth: 2.5,
                            pointRadius: 3,
                            pointBackgroundColor: '#00A3FF',
                            pointHoverRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    color: colors.grid,
                                    drawBorder: false
                                },
                                ticks: {
                                    color: colors.tick,
                                    font: {
                                        size: 11
                                    }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    color: colors.tick,
                                    font: {
                                        size: 11
                                    }
                                },
                                grid: {
                                    color: colors.grid,
                                    drawBorder: false
                                }
                            }
                        }
                    }
                });
            }

            build();
        })();

        // Content Overview Donut Chart
        (function() {
            const donutCtx = document.getElementById('contentDonutChart');
            if (!donutCtx) return;

            const contentData = [
                {{ $stats['dynamic_pages'] }},
                {{ $stats['video_tutorials'] }},
                {{ $stats['galleries'] }},
                {{ $stats['total_codes'] }}
            ];
            const contentColors = ['#6366f1', '#8b5cf6', '#10b981', '#f59e0b'];
            const contentLabels = ['Dynamic Pages', 'Video Tutorials', 'Dynamic Images', 'Premium Codes'];

            // If all values are 0, show a grey placeholder
            const total = contentData.reduce((a, b) => a + b, 0);
            const chartData = total === 0 ? [1] : contentData;
            const chartColors = total === 0 ? ['#374151'] : contentColors;

            new Chart(donutCtx, {
                type: 'doughnut',
                data: {
                    labels: total === 0 ? ['No Data'] : contentLabels,
                    datasets: [{
                        data: chartData,
                        backgroundColor: chartColors,
                        borderColor: document.documentElement.classList.contains('dark') ? '#1e293b' : '#ffffff',
                        borderWidth: 3,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '70%',
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: false }
                    }
                }
            });
        })();

        // User Details Modal Logic
        function showUser(button) {
            let user;
            try {
                user = JSON.parse(button.dataset.user || '{}');
            } catch (e) {
                toastr.error('Failed to load user details.');
                return;
            }

            function setText(id, value) {
                const el = document.getElementById(id);
                if (el) el.textContent = value || 'N/A';
            }

            setText('um_name', user.name);
            setText('um_email', user.email);
            setText('um_user_id', user.user_id);
            setText('um_plan', user.plan);
            setText('um_plan_expiry', user.premium_expires_at);
            setText('um_email_verified', user.email_verified_at);
            setText('um_gender', user.gender ? (user.gender.charAt(0).toUpperCase() + user.gender.slice(1)) : 'N/A');
            setText('um_background', user.background);
            setText('um_18_plus', user.is_18_plus);
            setText('um_provider', user.provider);
            setText('um_role', user.role ? (user.role.charAt(0).toUpperCase() + user.role.slice(1)) : 'User');
            setText('um_created_at', user.created_at || 'N/A');

            // Status badge
            const statusEl = document.getElementById('um_status');
            if (statusEl) {
                const status = (user.status || 'active').toLowerCase();
                statusEl.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                if (status === 'active') {
                    statusEl.className =
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300';
                } else {
                    statusEl.className =
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300';
                }
            }

            // Avatar
            const avatarImg = document.getElementById('um_avatar');
            const avatarPlaceholder = document.getElementById('um_avatar_placeholder');
            if (user.avatar) {
                avatarImg.src = user.avatar;
                avatarImg.classList.remove('hidden');
                avatarPlaceholder.classList.add('hidden');
            } else {
                avatarImg.removeAttribute('src');
                avatarImg.classList.add('hidden');
                avatarPlaceholder.classList.remove('hidden');
            }

            document.getElementById('userModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeUserModal() {
            const modal = document.getElementById('userModal');
            if (modal) modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // Delete User via AJAX with SweetAlert Confirmation
        function deleteUser(event, id) {
            event.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: 'This user will be permanently deleted.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('admin.users.destroy', ':id') }}'.replace(':id', id),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            toastr.success(res.message || 'User deleted successfully.');
                            const row = document.getElementById('recent-user-row-' + id);
                            if (row) {
                                $(row).fadeOut(300, function() {
                                    $(this).remove();
                                });
                            }
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message || 'Failed to delete user.');
                        }
                    });
                }
            });
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeUserModal();
            }
        });
    </script>
@endpush
