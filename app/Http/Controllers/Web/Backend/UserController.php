<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of users or return DataTable JSON.
     *
     * @param  Request  $request
     * @return View|JsonResponse
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = User::query()->latest();

            // Status filter
            if ($request->filled('status')) {
                $data->where('status', $request->status);
            }

            // Role filter
            if ($request->filled('role')) {
                $data->where('role', $request->role);
            }

            // Premium filter
            if ($request->filled('premium')) {
                if ($request->premium === 'premium') {
                    $data->where('is_premium', true);
                } elseif ($request->premium === 'free') {
                    $data->where('is_premium', false);
                }
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('avatar', function ($user) {
                    $avatar = $user->avatar
                        ? (str_starts_with($user->avatar, 'http') ? $user->avatar : asset($user->avatar))
                        : asset('backend/images/profile.png');

                    return '<img src="' . $avatar . '" alt="avatar" class="rounded-circle" style="width: 38px; height: 38px; object-fit: cover;" onerror="this.src=\'' . asset('backend/images/profile.png') . '\'">';
                })
                ->addColumn('user_id', function ($user) {
                    return e($user->user_id ?: 'USR-' . $user->id);
                })
                ->addColumn('name', function ($user) {
                    return e($user->name ?: 'N/A');
                })
                ->addColumn('email', function ($user) {
                    return e($user->email);
                })
                ->addColumn('role', function ($user) {
                    return e($user->role ?: 'User');
                })
                ->addColumn('plan', function ($user) {
                    return $user->is_premium ? 'Premium' : 'Free';
                })
                ->addColumn('status', function ($user) {
                    $checked = ($user->status ?? 'active') === 'active' ? 'checked' : '';
                    return '
                        <div class="form-check form-switch d-flex align-items-center">
                            <input onclick="showStatusChangeAlert(' . $user->id . ')"
                                   type="checkbox"
                                   class="form-check-input status-toggle"
                                   id="switch' . $user->id . '"
                                   data-id="' . $user->id . '"
                                   name="status" ' . $checked . '>
                        </div>
                    ';
                })
                ->addColumn('created_at_formatted', function ($user) {
                    return $user->created_at ? $user->created_at->format('M d, Y') : 'N/A';
                })
                ->addColumn('action', function ($user) {
                    return '
                        <div class="btn-group btn-group-sm" role="group">
                            <button onclick="showUserDetails(' . $user->id . ')" class="btn btn-primary btn-sm" title="View Details">
                                <i class="fa fa-eye"></i>
                            </button>
                            <button onclick="showDeleteConfirm(' . $user->id . ')" class="btn btn-danger btn-sm" title="Delete User">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['avatar', 'status', 'action'])
                ->make(true);
        }

        return view('backend.layouts.users.index');
    }

    /**
     * Fetch user details for the Show Modal.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $user = User::withCount(['faceScans', 'premiumCodes'])->findOrFail($id);

        $avatarUrl = $user->avatar
            ? (str_starts_with($user->avatar, 'http') ? $user->avatar : asset($user->avatar))
            : asset('backend/images/profile.png');

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                  => $user->id,
                'user_id'             => $user->user_id ?: 'USR-' . $user->id,
                'name'                => $user->name ?: 'N/A',
                'email'               => $user->email,
                'avatar'              => $avatarUrl,
                'role'                => $user->role ?: 'User',
                'status'              => ucfirst($user->status ?: 'active'),
                'gender'              => ucfirst($user->gender ?: 'N/A'),
                'background'          => ucfirst($user->background ?: 'N/A'),
                'is_18_plus'          => $user->is_18_plus ? 'Yes' : 'No',
                'provider'            => ucfirst($user->provider ?: 'Direct'),
                'plan'                => $user->is_premium ? 'Premium' : 'Free',
                'premium_expires_at'  => $user->premium_expires_at ? $user->premium_expires_at->format('M d, Y') : ($user->is_premium ? 'Lifetime' : 'N/A'),
                'email_verified_at'   => $user->email_verified_at ? $user->email_verified_at->format('M d, Y') : 'No',
                'created_at'          => $user->created_at ? $user->created_at->format('M d, Y') : 'N/A',
                'face_scans_count'    => $user->face_scans_count ?? 0,
            ],
        ]);
    }

    /**
     * Toggle user status (active / inactive).
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function status(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        if ($user->status === 'active') {
            $user->status = 'inactive';
            $user->save();

            return response()->json([
                'success' => false,
                'message' => 'User deactivated successfully.',
                'data'    => $user,
            ]);
        } else {
            $user->status = 'active';
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'User activated successfully.',
                'data'    => $user,
            ]);
        }
    }

    /**
     * Soft delete user account.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        if (auth()->id() === $id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.',
            ], 403);
        }

        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
        ]);
    }
}
