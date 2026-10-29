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
use Yajra\DataTables\Facades\DataTables;

class PremiumCodeController extends Controller
{
    // 0/O ar 1/I bad, jate kreta code vul na pore
    protected const ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    public function index(Request $request): View | JsonResponse
    {
        if ($request->ajax()) {
            $query = PremiumCode::with(['assignedUser:id,name,email', 'usedBy:id,name,email'])->latest('id');

            if ($status = $request->input('status')) {
                $query->withStatus($status);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('code_info', function (PremiumCode $code) {
                    $html = '<span class="font-mono text-xs font-semibold text-gray-800 dark:text-gray-100 bg-gray-100 dark:bg-gray-800 rounded-lg px-2 py-1">' . e($code->code) . '</span>';
                    if ($code->note) {
                        $html .= '<span class="block text-xs text-gray-400 mt-1">' . e($code->note) . '</span>';
                    }

                    return $html;
                })
                ->filterColumn('code_info', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('code', 'like', "%{$keyword}%")
                            ->orWhere('note', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('assigned_to', function (PremiumCode $code) {
                    return e($code->assignedUser?->email ?? '—');
                })
                ->addColumn('validity', function (PremiumCode $code) {
                    return $code->validity_days ? ($code->validity_days . ' days') : 'Lifetime';
                })
                ->addColumn('status', function (PremiumCode $code) {
                    $statusStyles = [
                        'unused'  => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400',
                        'used'    => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
                        'expired' => 'bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-400',
                    ];
                    $status = $code->status;
                    $badge  = '<span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[11px] font-semibold ' . ($statusStyles[$status] ?? $statusStyles['unused']) . '">' . ucfirst($status) . '</span>';

                    if ($status === 'used') {
                        return $badge;
                    }

                    $checked = ($status === 'unused') ? 'checked' : '';

                    return '<div class="flex items-center justify-center gap-2">
                        ' . $badge . '
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" onclick="changeStatus(event, ' . $code->id . ')" class="sr-only peer" ' . $checked . '>
                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[\'\'] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-green-500"></div>
                        </label>
                    </div>';
                })
                ->addColumn('used_by_user', function (PremiumCode $code) {
                    return e($code->usedBy?->email ?? '—');
                })
                ->addColumn('created_at', function (PremiumCode $code) {
                    return $code->created_at ? $code->created_at->format('d M, Y') : 'N/A';
                })
                ->addColumn('action', function (PremiumCode $code) {
                    $editBtn = '';
                    if ($code->status === 'unused') {
                        $editUrl = route('admin.premium_code.edit', $code->id);
                        $editBtn = '<a href="' . $editUrl . '"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-blue-600 bg-blue-100 hover:bg-blue-600 hover:text-white dark:text-blue-400 dark:bg-blue-900/30 dark:hover:bg-blue-600 dark:hover:text-white transition-all shadow-sm hover:shadow"
                            title="Edit Code">
                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                        </a>';
                    }

                    return '<div class="flex items-center justify-center gap-2">
                        ' . $editBtn . '
                        <button type="button"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-red-600 bg-red-100 hover:bg-red-600 hover:text-white dark:text-red-400 dark:bg-red-900/30 dark:hover:bg-red-600 dark:hover:text-white transition-all shadow-sm hover:shadow"
                            onclick="deleteRecord(event, ' . $code->id . ')"
                            title="Delete Code">
                            <i class="fa-solid fa-trash-can text-sm"></i>
                        </button>
                    </div>';
                })
                ->rawColumns(['code_info', 'status', 'action'])
                ->make(true);
        }

        return view('backend.v2.pages.premium_code.index');
    }

    public function create(): View|RedirectResponse
    {
        $users = User::select('id', 'name', 'email')->orderBy('name')->get();
        return view('backend.v2.pages.premium_code.create', compact('users'));
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

        return view('backend.v2.pages.premium_code.edit', compact('code', 'users'));
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

    public function status(int $id): JsonResponse
    {
        try {
            $code = PremiumCode::findOrFail($id);

            if ($code->used_at) {
                return response()->json([
                    't-error' => true,
                    'success' => false,
                    'message' => 'Used codes cannot be toggled.',
                ], 422);
            }

            if ($code->status === 'expired') {
                $code->code_expires_at = null;
                $code->save();

                return response()->json([
                    't-success' => true,
                    'success'   => true,
                    'message'   => 'Code reactivated successfully.',
                    'data'      => $code,
                ]);
            } else {
                $code->code_expires_at = now()->subMinute();
                $code->save();

                return response()->json([
                    't-success' => true,
                    'success'   => true,
                    'message'   => 'Code marked as expired.',
                    'data'      => $code,
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                't-error' => true,
                'success' => false,
                'message' => 'Failed to update code status: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $data = PremiumCode::findOrFail($id);

            if ($data->used_at) {
                return response()->json([
                    't-error'   => true,
                    'success'   => false,
                    'message'   => 'Used codes cannot be deleted.',
                ], 422);
            }

            $data->delete();

            return response()->json([
                't-success' => true,
                'success'   => true,
                'message'   => 'Code deleted successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                't-error' => true,
                'success' => false,
                'message' => 'Failed to delete code: ' . $e->getMessage(),
            ], 500);
        }
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
