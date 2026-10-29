<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\DynamicGallery;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class DynamicGalleryController extends Controller
{
    public function index(Request $request): View | JsonResponse
    {
        if ($request->ajax()) {
            $data = DynamicGallery::latest('id');

            if ($request->filled('type')) {
                $data->where('type', $request->input('type'));
            }

            if (!empty($request->input('search.value'))) {
                $searchTerm = $request->input('search.value');
                $data->where(function ($query) use ($searchTerm) {
                    $query->where('title', 'LIKE', "%$searchTerm%")
                          ->orWhere('subtitle', 'LIKE', "%$searchTerm%")
                          ->orWhere('description', 'LIKE', "%$searchTerm%")
                          ->orWhere('type', 'LIKE', "%$searchTerm%");
                });
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('type', function ($data) {
                    return strtolower($data->type ?? '') === 'side' ? 'Side View' : 'Front View';
                })
                ->addColumn('title', function ($data) {
                    return e($data->title);
                })
                ->addColumn('description', function ($data) {
                    $desc = $data->description ?? '';
                    return e(strlen($desc) > 80 ? substr($desc, 0, 80) . '...' : $desc);
                })
                ->addColumn('right_image', function ($data) {
                    $raw = $data->getRawOriginal('right_image');
                    if ($raw && file_exists(public_path($raw))) {
                        return '<img src="' . $data->right_image . '" alt="That\'s right" style="width: 45px; height: 45px; object-fit: cover; border-radius: 4px;">';
                    }
                    return '<span class="text-muted">No Image</span>';
                })
                ->addColumn('wrong_image', function ($data) {
                    $raw = $data->getRawOriginal('wrong_image');
                    if ($raw && file_exists(public_path($raw))) {
                        return '<img src="' . $data->wrong_image . '" alt="Not like that" style="width: 45px; height: 45px; object-fit: cover; border-radius: 4px;">';
                    }
                    return '<span class="text-muted">No Image</span>';
                })
                ->addColumn('status', function ($data) {
                    $checked = $data->status == "active" ? "checked" : "";
                    return '
                        <div class="form-check form-switch d-flex">
                            <input onclick="showStatusChangeAlert(' . $data->id . ')"
                                   type="checkbox"
                                   class="form-check-input status-toggle"
                                   id="switch' . $data->id . '"
                                   data-id="' . $data->id . '"
                                   name="status" ' . $checked . '>
                            <label class="form-check-label ms-2" for="switch' . $data->id . '"></label>
                        </div>
                    ';
                })
                ->addColumn('action', function ($data) {
                    return '<div class="btn-group btn-group-sm" role="group">
                                <a href="' . route('admin.dynamic_gallery.edit', ['id' => $data->id]) . '" class="btn btn-primary" title="Edit">
                                   <i class="fa fa-pencil me-1"></i> Edit
                                </a>
                                <button onclick="showDeleteConfirm(' . $data->id . ')" class="btn btn-danger ms-1" title="Delete">
                                   <i class="fa fa-trash me-1"></i> Delete
                                </button>
                            </div>';
                })
                ->rawColumns(['type', 'right_image', 'wrong_image', 'status', 'action'])
                ->make(true);
        }

        $canCreate = DynamicGallery::count() < 2;

        return view('backend.layouts.dynamic_gallery.index', compact('canCreate'));
    }

    public function create(): View | RedirectResponse
    {
        if (User::find(auth()->user()->id)) {
            $existingTypes = DynamicGallery::pluck('type')->toArray();
            if (in_array('front', $existingTypes) && in_array('side', $existingTypes)) {
                return redirect()->route('admin.dynamic_gallery.index')
                    ->with('t-info', 'Front View and Side View items are already created (1 each). You can edit them.');
            }
            return view('backend.layouts.dynamic_gallery.create', compact('existingTypes'));
        }
        return redirect()->route('admin.dynamic_gallery.index');
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            if (User::find(auth()->user()->id)) {
                $validator = Validator::make($request->all(), [
                    'type'        => 'required|in:front,side|unique:dynamic_galleries,type',
                    'title'       => 'required|string|max:255',
                    'subtitle'    => 'nullable|string|max:255',
                    'description' => 'nullable|string',
                    'right_image' => 'required|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
                    'wrong_image' => 'required|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
                ], [
                    'type.unique' => 'A ' . ($request->type === 'side' ? 'Side View' : 'Front View') . ' already exists (only 1 allowed). Please edit the existing one.',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                $rightImagePath = uploadImage($request->file('right_image'), 'dynamic_gallery');
                $wrongImagePath = uploadImage($request->file('wrong_image'), 'dynamic_gallery');

                DynamicGallery::create([
                    'type'        => $request->type,
                    'title'       => $request->title,
                    'subtitle'    => $request->subtitle ?: (strtolower($request->type) === 'side' ? 'SIDE VIEW' : 'FRONT VIEW'),
                    'description' => $request->description,
                    'right_image' => $rightImagePath,
                    'wrong_image' => $wrongImagePath,
                    'right_label' => "That's right",
                    'wrong_label' => "Not like that",
                    'status'      => 'active',
                ]);
            }
            return redirect()->route('admin.dynamic_gallery.index')->with('t-success', 'Created successfully.');
        } catch (Exception $e) {
            return redirect()->route('admin.dynamic_gallery.index')->with('t-error', 'Creation failed: ' . $e->getMessage());
        }
    }

    public function edit(int $id): View | RedirectResponse
    {
        if (User::find(auth()->user()->id)) {
            $data = DynamicGallery::findOrFail($id);
            return view('backend.layouts.dynamic_gallery.edit', compact('data'));
        }
        return redirect()->route('admin.dynamic_gallery.index');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        try {
            if (User::find(auth()->user()->id)) {
                $gallery = DynamicGallery::findOrFail($id);

                $validator = Validator::make($request->all(), [
                    'type'        => 'required|in:front,side|unique:dynamic_galleries,type,' . $id,
                    'title'       => 'required|string|max:255',
                    'subtitle'    => 'nullable|string|max:255',
                    'description' => 'nullable|string',
                    'right_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
                    'wrong_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
                ], [
                    'type.unique' => 'A ' . ($request->type === 'side' ? 'Side View' : 'Front View') . ' already exists.',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                $rightImagePath = $gallery->getRawOriginal('right_image');
                if ($request->hasFile('right_image')) {
                    if ($rightImagePath && file_exists(public_path($rightImagePath)) && !str_contains($rightImagePath, 'sample')) {
                        @unlink(public_path($rightImagePath));
                    }
                    $rightImagePath = uploadImage($request->file('right_image'), 'dynamic_gallery');
                }

                $wrongImagePath = $gallery->getRawOriginal('wrong_image');
                if ($request->hasFile('wrong_image')) {
                    if ($wrongImagePath && file_exists(public_path($wrongImagePath)) && !str_contains($wrongImagePath, 'sample')) {
                        @unlink(public_path($wrongImagePath));
                    }
                    $wrongImagePath = uploadImage($request->file('wrong_image'), 'dynamic_gallery');
                }

                $gallery->update([
                    'type'        => $request->type,
                    'title'       => $request->title,
                    'subtitle'    => $request->subtitle ?: (strtolower($request->type) === 'side' ? 'SIDE VIEW' : 'FRONT VIEW'),
                    'description' => $request->description,
                    'right_image' => $rightImagePath,
                    'wrong_image' => $wrongImagePath,
                ]);
            }
            return redirect()->route('admin.dynamic_gallery.index')->with('t-success', 'Updated successfully.');
        } catch (Exception $e) {
            return redirect()->route('admin.dynamic_gallery.index')->with('t-error', 'Update failed: ' . $e->getMessage());
        }
    }

    public function status(int $id): JsonResponse
    {
        $data = DynamicGallery::findOrFail($id);
        if ($data->status === 'active') {
            $data->status = 'inactive';
            $data->save();
            return response()->json([
                'success' => false,
                'message' => 'Status changed to Inactive.',
                'data'    => $data,
            ]);
        } else {
            $data->status = 'active';
            $data->save();
            return response()->json([
                'success' => true,
                'message' => 'Status changed to Active.',
                'data'    => $data,
            ]);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        $gallery = DynamicGallery::findOrFail($id);
        $rightImg = $gallery->getRawOriginal('right_image');
        $wrongImg = $gallery->getRawOriginal('wrong_image');

        if ($rightImg && file_exists(public_path($rightImg)) && !str_contains($rightImg, 'sample')) {
            @unlink(public_path($rightImg));
        }
        if ($wrongImg && file_exists(public_path($wrongImg)) && !str_contains($wrongImg, 'sample')) {
            @unlink(public_path($wrongImg));
        }

        $gallery->delete();

        return response()->json([
            't-success' => true,
            'message'   => 'Deleted successfully.',
        ]);
    }
}
