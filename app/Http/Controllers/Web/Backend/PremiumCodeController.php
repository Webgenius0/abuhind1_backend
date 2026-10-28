<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Mail\PromoCodeMail;
use App\Models\PremiumCode;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class PremiumCodeController extends Controller
{
    // 0/O ar 1/I bad, jate kreta code vul na pore
    protected const ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    public function index(Request $request): View|JsonResponse
    {
        Log::info('premium-code index hit', [
            'ajax'   => $request->ajax(),
            'wants'  => $request->wantsJson(),
            'header' => $request->header('X-Requested-With'),
            'method' => $request->method(),
        ]);

        if ($request->ajax()) {
            $data = PremiumCode::with(['assignedUser:id,name,email', 'usedBy:id,name,email'])->latest();

            if (!empty($request->input('search.value'))) {
                $searchTerm = $request->input('search.value');
                $data->where('code', 'LIKE', "%$searchTerm%");
            }

            if ($request->filled('status')) {
                $data->withStatus($request->input('status'));
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('assigned_user', function ($data) {
                    return $data->assignedUser?->email ?? '—';
                })
                ->addColumn('used_by', function ($data) {
                    return $data->usedBy?->email ?? '—';
                })
                ->addColumn('validity', function ($data) {
                    return $data->validity_days ? $data->validity_days . ' days' : 'Lifetime';
                })
                ->addColumn('status', function ($data) {
                    $badge = match ($data->status) {
                        'used'    => 'bg-secondary',
                        'expired' => 'bg-danger',
                        default   => 'bg-success',
                    };
                    return '<span class="badge ' . $badge . '">' . ucfirst($data->status) . '</span>';
                })
                ->addColumn('action', function ($data) {
                    if ($data->status !== 'unused') {
                        return '';
                    }
                    $editUrl = route('admin.premium_code.edit', $data->id);
                    return '<div class="btn-group btn-group-sm" role="group">
                                <a href="' . $editUrl . '" class="text-white btn btn-primary" title="Edit">
                                   <i class="fa fa-edit" aria-hidden="true"></i>
                                </a>
                                <a href="javascript:void(0)" onclick="showDeleteAlert(' . $data->id . ')" class="text-white btn btn-danger" title="Delete">
                                   <i class="fa fa-trash" aria-hidden="true"></i>
                                </a>
                            </div>';
                })
                ->rawColumns(['status', 'action'])
                ->make();
        }
        return view('backend.layouts.premium_code.index');
    }

    public function create(): View|RedirectResponse
    {
        $users = User::select('id', 'name', 'email')->orderBy('name')->get();
        return view('backend.layouts.premium_code.create', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'code'            => 'nullable|string|max:64|unique:premium_codes,code',
                'count'           => 'nullable|integer|min:1|max:500',
                'validity_days'   => 'nullable|integer|min:1|max:3650',
                'code_expires_at' => 'nullable|date',
                'user_id'         => 'nullable|integer|exists:users,id',
                'note'            => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            if ($request->filled('code')) {
                // Custom code provided by admin
                $codes = [strtoupper(trim($request->code))];
            } else {
                // Auto-generate codes if left blank (if user_id assigned, exactly 1)
                $count = $request->filled('user_id') ? 1 : (int) ($request->input('count') ?: 1);
                $codes = $this->generateCodes($count);
            }

            $assignedUserId = $request->filled('user_id') ? (int) $request->user_id : null;
            $assignedUser = $assignedUserId ? User::find($assignedUserId) : null;

            foreach ($codes as $code) {
                $premiumCode = PremiumCode::create([
                    'code'             => $code,
                    'validity_days'    => $request->validity_days ?: null,
                    'code_expires_at'  => $request->filled('code_expires_at')
                        ? Carbon::parse($request->code_expires_at)
                        : null,
                    'assigned_user_id' => $assignedUserId,
                    'created_by'       => auth()->id(),
                    'note'             => $request->note,
                ]);

                // Automatically email the promo code if assigned to a user
                if ($assignedUser && $assignedUser->email) {
                    try {
                        Mail::to($assignedUser->email)->send(new PromoCodeMail($assignedUser, $code, $premiumCode));
                    } catch (\Throwable $e) {
                        Log::error('Failed to send promo code email: ' . $e->getMessage());
                    }
                }
            }

            $msg = count($codes) . ' premium code(s) generated successfully.';
            if ($assignedUser && $assignedUser->email) {
                $msg .= ' Promo code sent to ' . $assignedUser->email . '.';
            }

            return redirect()->route('admin.premium_code.index')
                ->with('success', $msg)
                ->with('t-success', $msg);
        } catch (Exception $e) {
            Log::error('Premium code generation failed: ' . $e->getMessage());
            return redirect()->route('admin.premium_code.index')
                ->with('error', 'Premium code generation failed.')
                ->with('t-error', 'Premium code generation failed.');
        }
    }

    public function edit(int $id): View|RedirectResponse
    {
        $code = PremiumCode::findOrFail($id);

        if ($code->status !== 'unused') {
            return redirect()->route('admin.premium_code.index')
                ->with('error', 'Only unused codes can be edited.')
                ->with('t-error', 'Only unused codes can be edited.');
        }

        $users = User::select('id', 'name', 'email')->orderBy('name')->get();

        return view('backend.layouts.premium_code.edit', compact('code', 'users'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        try {
            $premiumCode = PremiumCode::findOrFail($id);

            if ($premiumCode->status !== 'unused') {
                return redirect()->route('admin.premium_code.index')
                    ->with('error', 'Only unused codes can be edited.')
                    ->with('t-error', 'Only unused codes can be edited.');
            }

            $validator = Validator::make($request->all(), [
                'code'            => 'required|string|max:64|unique:premium_codes,code,' . $premiumCode->id,
                'validity_days'   => 'nullable|integer|min:1|max:3650',
                'code_expires_at' => 'nullable|date',
                'user_id'         => 'nullable|integer|exists:users,id',
                'note'            => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $assignedUserId = $request->filled('user_id') ? (int) $request->user_id : null;
            $assignedUser = $assignedUserId ? User::find($assignedUserId) : null;
            $newCode = strtoupper(trim($request->code));

            $premiumCode->update([
                'code'             => $newCode,
                'validity_days'    => $request->validity_days ?: null,
                'code_expires_at'  => $request->filled('code_expires_at')
                    ? Carbon::parse($request->code_expires_at)
                    : null,
                'assigned_user_id' => $assignedUserId,
                'note'             => $request->note,
            ]);

            // When assigned to a user (or mail selected to update), send mail to that user
            if ($assignedUser && $assignedUser->email) {
                try {
                    Mail::to($assignedUser->email)->send(new PromoCodeMail($assignedUser, $newCode, $premiumCode));
                } catch (\Throwable $e) {
                    Log::error('Failed to send promo code email on update: ' . $e->getMessage());
                }
            }

            $msg = 'Premium code updated successfully.';
            if ($assignedUser && $assignedUser->email) {
                $msg .= ' Promo code sent to ' . $assignedUser->email . '.';
            }

            return redirect()->route('admin.premium_code.index')
                ->with('success', $msg)
                ->with('t-success', $msg);
        } catch (Exception $e) {
            Log::error('Premium code update error: ' . $e->getMessage());
            return redirect()->route('admin.premium_code.index')
                ->with('error', 'Premium code update failed.')
                ->with('t-error', 'Premium code update failed.');
        }
    }

    public function destroy(int $id): JsonResponse
    {
        $data = PremiumCode::findOrFail($id);

        if ($data->used_at) {
            return response()->json([
                't-success' => false,
                'message'   => 'Used codes cannot be deleted.',
            ], 422);
        }

        $data->delete();

        return response()->json([
            't-success' => true,
            'message'   => 'Deleted successfully.',
        ]);
    }

    /**
     * @return array<int, string>
     */
    protected function generateCodes(int $count): array
    {
        $generated = [];

        while (count($generated) < $count) {
            $code = $this->makeCode();

            if (in_array($code, $generated, true) || PremiumCode::where('code', $code)->exists()) {
                continue;
            }

            $generated[] = $code;
        }

        return $generated;
    }

    protected function makeCode(): string
    {
        $chars = '';

        for ($i = 0; $i < 12; $i++) {
            $chars .= self::ALPHABET[random_int(0, strlen(self::ALPHABET) - 1)];
        }

        return 'PRM-' . implode('-', str_split($chars, 4));
    }
}
