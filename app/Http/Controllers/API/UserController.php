<?php

namespace App\Http\Controllers\Api;

use App\Contracts\AuthServiceInterface;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\FaceScanRepositoryInterface;
use App\Services\AvatarUploadService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use ApiResponse;

    public function __construct(
        private AvatarUploadService  $avatarService,
        private AuthServiceInterface $authService,
        protected FaceScanRepositoryInterface $faceScans,
    ) {
    }


    public function userDetails(Request $request)
    {
        $user = Auth::user();
        $latestScan = $this->faceScans->forUser($user->id)->first(); // forUser() already orders latest()

        $data = $user->toArray();

        $data['plan']   = $user->isPremium() ? 'pro' : 'free';
        $data['status'] = 'active';

        $data['latest_front_image_url'] = $latestScan?->front_image_path
            ? Storage::disk('public')->url($latestScan->front_image_path)
            : null;

        $data['latest_side_image_url'] = $latestScan?->side_image_path
            ? Storage::disk('public')->url($latestScan->side_image_path)
            : null;

        $data['report_pdf_url'] = $latestScan?->report_pdf_url;

        return $this->success($data, 'User Data fetch Successful!', 200);
    }


    public function updateUser(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name'           => 'sometimes|nullable|string|max:255',
            'avatar'         => 'nullable|image|mimes:jpeg,png,jpg,svg|max:20480',
            'gender'         => 'sometimes|nullable|string|max:50',
            'background'     => 'sometimes|nullable|string|max:255',
            'is_18_plus'     => 'sometimes|boolean',
            'agree_to_terms' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation Error', $validator->errors(), 422);
        }

        if ($request->filled('name')) {
            $user->name = $request->name;
        }

        if ($request->hasFile('avatar')) {
            $user->avatar = $this->avatarService->upload($request->file('avatar'));
        }

        foreach (['gender', 'background'] as $field) {
            if ($request->has($field)) {
                $user->{$field} = $request->input($field);
            }
        }

        foreach (['is_18_plus', 'agree_to_terms'] as $field) {
            if ($request->has($field)) {
                $user->{$field} = $request->boolean($field);
            }
        }

        $user->save();

        return $this->success('Profile updated successfully', $user->fresh(), 200);
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }

        if (!$this->authService->checkPassword($user, $request->old_password)) {
            return $this->error([], 'Old password is incorrect', 400);
        }

        $this->authService->resetPassword($user, $request->new_password);

        return $this->success([], 'Password updated successfully', 200);
    }

    public function logoutUser(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success([], 'User logged out successfully', 200);
    }

    public function deleteAccount(Request $request)
    {
        $user = Auth::user();

        $user->delete();

        return $this->success([], 'Your account has been deleted successfully', 200);
    }
}
