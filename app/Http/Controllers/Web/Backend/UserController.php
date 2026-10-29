<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of users via Yajra DataTables (AJAX) or view (Web).
     */
    public function index(Request $request): View | JsonResponse
    {
        if ($request->ajax()) {
            $query = User::query()->latest('id');

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('avatar', function (User $user) {
                    if ($user->avatar) {
                        $src = str_starts_with($user->avatar, 'http') ? $user->avatar : asset($user->avatar);
                        return '<img src="' . e($src) . '" alt="' . e($user->name) . '" class="w-12 h-12 rounded-lg object-cover border border-gray-200 dark:border-gray-700">';
                    }

                    return '<div class="w-12 h-12 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-400"><i class="fa-solid fa-image"></i></div>';
                })
                ->addColumn('name', function (User $user) {
                    $name   = ! empty($user->name) ? e($user->name) : 'N/A';
                    $userId = ! empty($user->user_id) ? e($user->user_id) : ('USR-' . $user->id);

                    return '<span class="font-medium text-gray-800 dark:text-gray-100">' . $name . '</span>' .
                           '<span class="block text-xs text-gray-400">' . $userId . '</span>';
                })
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%")
                            ->orWhere('user_id', 'like', "%{$keyword}%");
                    });
                })
                ->orderColumn('name', function ($query, $order) {
                    $query->orderBy('name', $order);
                })
                ->editColumn('email', function (User $user) {
                    return ! empty($user->email) ? e($user->email) : 'N/A';
                })
                ->addColumn('status', function (User $user) {
                    $checked = ($user->status ?? 'active') === 'active' ? 'checked' : '';

                    return '<label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" onclick="changeStatus(event, ' . $user->id . ')"
                            class="sr-only peer" ' . $checked . '>
                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[\'\'] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-green-500"></div>
                    </label>';
                })
                ->addColumn('created_at', function (User $user) {
                    return $user->created_at ? $user->created_at->format('d M, Y') : 'N/A';
                })
                ->addColumn('action', function (User $user) {
                    $avatarUrl = $user->avatar
                        ? (str_starts_with($user->avatar, 'http') ? $user->avatar : asset($user->avatar))
                        : null;

                    $payload = e(json_encode([
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
                        'plan'               => $user->is_premium ? 'Premium' : 'Free',
                        'premium_expires_at' => $user->premium_expires_at ? $user->premium_expires_at->format('M d, Y') : ($user->is_premium ? 'Lifetime' : 'N/A'),
                        'email_verified_at'  => $user->email_verified_at ? $user->email_verified_at->format('M d, Y') : 'No',
                        'created_at'         => $user->created_at ? $user->created_at->format('d M, Y') : 'N/A',
                    ]));

                    return '<div class="flex items-center justify-center gap-2">
                        <button type="button"
                            data-user="' . $payload . '"
                            onclick="showUser(this)"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-blue-600 bg-blue-100 hover:bg-blue-600 hover:text-white dark:text-blue-400 dark:bg-blue-900/30 dark:hover:bg-blue-600 dark:hover:text-white transition-all shadow-sm hover:shadow"
                            title="View User">
                            <i class="fa-solid fa-eye text-sm"></i>
                        </button>
                        <button type="button"
                            data-user="' . $payload . '"
                            onclick="openEditUserModal(this)"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-emerald-600 bg-emerald-100 hover:bg-emerald-600 hover:text-white dark:text-emerald-400 dark:bg-emerald-900/30 dark:hover:bg-emerald-600 dark:hover:text-white transition-all shadow-sm hover:shadow"
                            title="Edit User">
                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                        </button>
                        <button type="button"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-red-600 bg-red-100 hover:bg-red-600 hover:text-white dark:text-red-400 dark:bg-red-900/30 dark:hover:bg-red-600 dark:hover:text-white transition-all shadow-sm hover:shadow"
                            onclick="deleteRecord(event, ' . $user->id . ')"
                            title="Delete User">
                            <i class="fa-solid fa-trash-can text-sm"></i>
                        </button>
                    </div>';
                })
                ->rawColumns(['avatar', 'name', 'status', 'action'])
                ->make(true);
        }

        return view('backend.v2.pages.users.index');
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        return view('backend.v2.pages.users.create');
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role'     => 'nullable|string|in:User,Admin',
            'status'   => 'nullable|string|in:active,inactive,suspended,blocked',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        do {
            $userId = 'USR-' . strtoupper(Str::random(6));
        } while (User::where('user_id', $userId)->exists());

        User::create([
            'user_id'  => $userId,
            'name'     => $request->input('name'),
            'email'    => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role'     => $request->input('role', 'User'),
            'status'   => $request->input('status', 'active'),
        ]);

        return redirect()->route('admin.users.index')->with('t-success', 'User created successfully.');
    }

    /**
     * Fetch user details for the Show Modal.
     */
    public function show(int $id): JsonResponse
    {
        $user = User::withCount(['faceScans', 'premiumCodes'])->findOrFail($id);

        $avatarUrl = $user->avatar
            ? (str_starts_with($user->avatar, 'http') ? $user->avatar : asset($user->avatar))
            : asset('backend/images/profile.png');

        return response()->json([
            'success'   => true,
            't-success' => true,
            'data'      => [
                'id'                 => $user->id,
                'user_id'            => $user->user_id ?: ('USR-' . $user->id),
                'name'               => $user->name ?: 'N/A',
                'email'              => $user->email,
                'avatar'             => $avatarUrl,
                'role'               => $user->role ?: 'User',
                'status'             => ucfirst($user->status ?: 'active'),
                'gender'             => ucfirst($user->gender ?: 'N/A'),
                'background'         => ucfirst($user->background ?: 'N/A'),
                'is_18_plus'         => $user->is_18_plus ? 'Yes' : 'No',
                'provider'           => ucfirst($user->provider ?: 'Direct'),
                'plan'               => $user->is_premium ? 'Premium' : 'Free',
                'premium_expires_at' => $user->premium_expires_at ? $user->premium_expires_at->format('M d, Y') : ($user->is_premium ? 'Lifetime' : 'N/A'),
                'email_verified_at'  => $user->email_verified_at ? $user->email_verified_at->format('M d, Y') : 'No',
                'created_at'         => $user->created_at ? $user->created_at->format('M d, Y') : 'N/A',
                'face_scans_count'   => $user->face_scans_count ?? 0,
            ],
        ]);
    }

    /**
     * Update user details from within the update modal via AJAX.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $user = User::query()->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|max:255|unique:users,email,' . $id,
                'role'     => 'nullable|string|in:User,Admin',
                'status'   => 'nullable|string|in:active,inactive',
                'password' => 'nullable|string|min:8',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    't-error' => true,
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            $user->name  = $request->input('name');
            $user->email = $request->input('email');
            if ($request->filled('role')) {
                $user->role = $request->input('role');
            }
            if ($request->filled('status')) {
                $user->status = $request->input('status');
            }
            if ($request->filled('password')) {
                $user->password = Hash::make($request->input('password'));
            }
            $user->save();

            return response()->json([
                'success'   => true,
                't-success' => true,
                'message'   => 'User updated successfully.',
                'data'      => $user,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                't-error' => true,
                'message' => 'Failed to update user: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle user status (active / inactive).
     */
    public function status(int $id): JsonResponse
    {
        try {
            $user = User::query()->findOrFail($id);
            $user->status = ($user->status === 'active') ? 'inactive' : 'active';
            $user->save();

            return response()->json([
                'success'   => true,
                't-success' => true,
                'message'   => 'Status changed successfully.',
                'data'      => $user,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                't-error' => true,
                'message' => 'Failed to update user status.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Soft delete user account.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            if (auth()->id() === $id) {
                return response()->json([
                    'success' => false,
                    't-error' => true,
                    'message' => 'You cannot delete your own account.',
                ], 403);
            }

            $user = User::query()->findOrFail($id);
            $user->delete();

            return response()->json([
                'success'   => true,
                't-success' => true,
                'message'   => 'User deleted successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                't-error' => true,
                'message' => 'Failed to delete user.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
