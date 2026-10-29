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
use Illuminate\Support\Str;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class DynamicGalleryController extends Controller
{
    public function index(Request $request): View | JsonResponse
    {
        if ($request->ajax()) {
            $query = DynamicGallery::query()->latest('id');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('type_badge', function (DynamicGallery $gallery) {
                    if (strtolower((string) $gallery->type) === 'side') {
                        return '<span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[11px] font-semibold bg-violet-50 text-violet-700 dark:bg-violet-950/50 dark:text-violet-400">Side View</span>';
                    }

                    return '<span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[11px] font-semibold bg-primary-50 text-primary-700 dark:bg-primary-950/50 dark:text-primary-400">Front View</span>';
                })
                ->addColumn('title_info', function (DynamicGallery $gallery) {
                    return '<span class="font-medium text-gray-800 dark:text-gray-100">' . e($gallery->title) . '</span>' .
                           '<span class="block text-xs text-gray-400">' . e($gallery->subtitle) . '</span>';
                })
                ->editColumn('description', function (DynamicGallery $gallery) {
                    return e(Str::limit(strip_tags((string) $gallery->description), 80) ?: 'N/A');
                })
                ->addColumn('right_image_render', function (DynamicGallery $gallery) {
                    if ($gallery->right_image) {
                        return '<a href="' . e($gallery->right_image) . '" target="_blank" class="inline-block group" title="Click to view full image">
                            <img src="' . e($gallery->right_image) . '" alt="That\'s Right"
                                class="w-16 h-16 rounded-xl object-cover border-2 border-emerald-300 dark:border-emerald-700 shadow-sm group-hover:scale-105 transition-transform"
                                style="width: 64px; height: 64px; min-width: 64px; min-height: 64px; max-width: 64px; max-height: 64px; object-fit: cover; border-radius: 0.75rem;">
                        </a>';
                    }

                    return '<span class="text-xs text-gray-400">No image</span>';
                })
                ->addColumn('wrong_image_render', function (DynamicGallery $gallery) {
                    if ($gallery->wrong_image) {
                        return '<a href="' . e($gallery->wrong_image) . '" target="_blank" class="inline-block group" title="Click to view full image">
                            <img src="' . e($gallery->wrong_image) . '" alt="Not Like That"
                                class="w-16 h-16 rounded-xl object-cover border-2 border-red-300 dark:border-red-700 shadow-sm group-hover:scale-105 transition-transform"
                                style="width: 64px; height: 64px; min-width: 64px; min-height: 64px; max-width: 64px; max-height: 64px; object-fit: cover; border-radius: 0.75rem;">
                        </a>';
                    }

                    return '<span class="text-xs text-gray-400">No image</span>';
                })
                ->addColumn('status', function (DynamicGallery $gallery) {
                    $checked = ($gallery->status ?? 'active') === 'active' ? 'checked' : '';

                    return '<label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" onclick="changeStatus(event, ' . $gallery->id . ')"
                            class="sr-only peer" ' . $checked . '>
                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[\'\'] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-green-500"></div>
                    </label>';
                })
                ->addColumn('action', function (DynamicGallery $gallery) {
                    $editUrl = route('admin.dynamic_gallery.edit', $gallery->id);

                    return '<div class="flex items-center justify-center gap-2">
                        <a href="' . $editUrl . '"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-blue-600 bg-blue-100 hover:bg-blue-600 hover:text-white dark:text-blue-400 dark:bg-blue-900/30 dark:hover:bg-blue-600 dark:hover:text-white transition-all shadow-sm hover:shadow"
                            title="Edit">
                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                        </a>
                        <button type="button"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-red-600 bg-red-100 hover:bg-red-600 hover:text-white dark:text-red-400 dark:bg-red-900/30 dark:hover:bg-red-600 dark:hover:text-white transition-all shadow-sm hover:shadow"
                            onclick="deleteRecord(event, ' . $gallery->id . ')"
                            title="Delete">
                            <i class="fa-solid fa-trash-can text-sm"></i>
                        </button>
                    </div>';
                })
                ->rawColumns(['type_badge', 'title_info', 'right_image_render', 'wrong_image_render', 'status', 'action'])
                ->make(true);
        }

        $galleries = DynamicGallery::latest('id')->get();
        $canCreate = $galleries->count() < 2;

        return view('backend.v2.pages.dynamic_gallery.index', compact('galleries', 'canCreate'));
    }

    public function create(): View | RedirectResponse
    {
        if (User::find(auth()->user()->id)) {
            $existingTypes = DynamicGallery::pluck('type')->toArray();
            if (in_array('front', $existingTypes) && in_array('side', $existingTypes)) {
                return redirect()->route('admin.dynamic_gallery.index')
                    ->with('t-info', 'Front View and Side View items are already created (1 each). You can edit them.');
            }
            return view('backend.v2.pages.dynamic_gallery.create', compact('existingTypes'));
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
            return view('backend.v2.pages.dynamic_gallery.edit', compact('data'));
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
        try {
            $data = DynamicGallery::findOrFail($id);
            $data->status = ($data->status === 'active') ? 'inactive' : 'active';
            $data->save();

            return response()->json([
                't-success' => true,
                'success'   => true,
                'message'   => 'Status changed successfully.',
                'data'      => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                't-error' => true,
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
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
                'success'   => true,
                'message'   => 'Image set deleted successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                't-error' => true,
                'success' => false,
                'message' => 'Failed to delete: ' . $e->getMessage(),
            ], 500);
        }
    }
}
