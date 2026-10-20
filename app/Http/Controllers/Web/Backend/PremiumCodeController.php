<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\PremiumCode;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class PremiumCodeController extends Controller
{
    // 0/O ar 1/I bad, jate kreta code vul na pore
    protected const ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    public function index(Request $request): View|JsonResponse
    {
        \Log::info('premium-code index hit', [
        'ajax'    => $request->ajax(),
        'wants'   => $request->wantsJson(),
        'header'  => $request->header('X-Requested-With'),
        'method'  => $request->method(),
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
                    return '<div class="btn-group btn-group-sm" role="group">
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
        if (User::find(auth()->user()->id)) {
            $users = User::select('id', 'name', 'email')->orderBy('name')->get();
            return view('backend.layouts.premium_code.create', compact('users'));
        }
        return redirect()->route('admin.premium_code.index');
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            if (User::find(auth()->user()->id)) {
                $validator = Validator::make($request->all(), [
                    'code'            => 'nullable|string|max:64|unique:premium_codes,code',
                    'count'           => 'required_without_all:user_id,code|nullable|integer|min:1|max:500',
                    'validity_days'   => 'nullable|integer|min:1|max:3650',
                    'code_expires_at' => 'nullable|date|after:now',
                    'user_id'         => 'nullable|integer|exists:users,id',
                    'note'            => 'nullable|string|max:255',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                if ($request->filled('code')) {
                    // Admin নিজে হাতে code লিখেছে — এটাই একমাত্র code
                    $codes = [strtoupper(trim($request->code))];
                } else {
                    // খালি রাখলে আগের মতো auto-generate
                    $count = $request->filled('user_id') ? 1 : (int) $request->count;
                    $codes = $this->generateCodes($count);
                }

                foreach ($codes as $code) {
                    PremiumCode::create([
                        'code'             => $code,
                        'validity_days'    => $request->validity_days ?: null,
                        'code_expires_at'  => $request->filled('code_expires_at')
                            ? now()->parse($request->code_expires_at)
                            : null,
                        'assigned_user_id' => $request->user_id ?: null,
                        'created_by'       => auth()->user()->id,
                        'note'             => $request->note,
                    ]);
                }

                return redirect()->route('admin.premium_code.index')
                    ->with('t-success', count($codes) . ' premium code(s) generated successfully.');
            }
            return redirect()->route('admin.premium_code.index');
        } catch (Exception) {
            return redirect()->route('admin.premium_code.index')->with('t-error', 'Premium code generation failed.');
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
