<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\VideoTutorial;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class VideoTutorialController extends Controller
{
    /**
     * Display a listing of video tutorials via Yajra DataTables (AJAX) or view (Web).
     */
    public function index(Request $request): View | JsonResponse
    {
        if ($request->ajax()) {
            $query = VideoTutorial::query()->latest('id');

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('title', function (VideoTutorial $tutorial) {
                    return ! empty($tutorial->title) ? e($tutorial->title) : 'N/A';
                })
                ->editColumn('description', function (VideoTutorial $tutorial) {
                    return e(Str::limit((string) $tutorial->description, 80) ?: 'N/A');
                })
                ->addColumn('youtube_link', function (VideoTutorial $tutorial) {
                    if ($tutorial->youtube_link) {
                        return '<a href="' . e($tutorial->youtube_link) . '" target="_blank" rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 text-primary-600 dark:text-primary-400 hover:underline">
                            <i class="fa-brands fa-youtube text-red-500 text-base"></i> Watch Video
                        </a>';
                    }

                    return '<span class="text-xs text-gray-400">N/A</span>';
                })
                ->addColumn('status', function (VideoTutorial $tutorial) {
                    $checked = ($tutorial->status ?? 'active') === 'active' ? 'checked' : '';

                    return '<label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" onclick="changeStatus(event, ' . $tutorial->id . ')"
                            class="sr-only peer" ' . $checked . '>
                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[\'\'] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-green-500"></div>
                    </label>';
                })
                ->addColumn('action', function (VideoTutorial $tutorial) {
                    $editUrl = route('admin.video_tutorial.edit', $tutorial->id);

                    return '<div class="flex items-center justify-center gap-2">
                        <a href="' . $editUrl . '"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-blue-600 bg-blue-100 hover:bg-blue-600 hover:text-white dark:text-blue-400 dark:bg-blue-900/30 dark:hover:bg-blue-600 dark:hover:text-white transition-all shadow-sm hover:shadow"
                            title="Edit Video">
                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                        </a>
                        <button type="button"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-red-600 bg-red-100 hover:bg-red-600 hover:text-white dark:text-red-400 dark:bg-red-900/30 dark:hover:bg-red-600 dark:hover:text-white transition-all shadow-sm hover:shadow"
                            onclick="deleteRecord(event, ' . $tutorial->id . ')"
                            title="Delete Video">
                            <i class="fa-solid fa-trash-can text-sm"></i>
                        </button>
                    </div>';
                })
                ->rawColumns(['youtube_link', 'status', 'action'])
                ->make(true);
        }

        return view('backend.v2.pages.video_tutorial.index');
    }

    public function create(): View | RedirectResponse
    {
        if (User::find(auth()->user()->id)) {
            return view('backend.v2.pages.video_tutorial.create');
        }
        return redirect()->route('admin.video_tutorial.index');
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            if (User::find(auth()->user()->id)) {
                $validator = Validator::make($request->all(), [
                    'title'        => 'required|string|max:255',
                    'description'  => 'nullable|string',
                    'youtube_link' => 'required|url',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                VideoTutorial::create([
                    'title'        => $request->title,
                    'description'  => $request->description,
                    'youtube_link' => $request->youtube_link,
                ]);
            }
            return redirect()->route('admin.video_tutorial.index')->with('t-success', 'Video Tutorial created successfully.');
        } catch (Exception) {
            return redirect()->route('admin.video_tutorial.index')->with('t-error', 'Video Tutorial creation failed.');
        }
    }

    public function edit(int $id): View | RedirectResponse
    {
        if (User::find(auth()->user()->id)) {
            $data = VideoTutorial::findOrFail($id);
            return view('backend.v2.pages.video_tutorial.edit', compact('data'));
        }
        return redirect()->route('admin.video_tutorial.index');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        try {
            if (User::find(auth()->user()->id)) {
                $validator = Validator::make($request->all(), [
                    'title'        => 'required|string|max:255',
                    'description'  => 'nullable|string',
                    'youtube_link' => 'required|url',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                $data = VideoTutorial::findOrFail($id);
                $data->update([
                    'title'        => $request->title,
                    'description'  => $request->description,
                    'youtube_link' => $request->youtube_link,
                ]);

                return redirect()->route('admin.video_tutorial.index')->with('t-success', 'Video Tutorial updated successfully.');
            }
        } catch (Exception) {
            return redirect()->route('admin.video_tutorial.index')->with('t-error', 'Video Tutorial update failed.');
        }
        return redirect()->route('admin.video_tutorial.index');
    }

    public function status(int $id): JsonResponse
    {
        try {
            $data = VideoTutorial::findOrFail($id);
            $data->status = ($data->status === 'active') ? 'inactive' : 'active';
            $data->save();

            return response()->json([
                't-success' => true,
                'success'   => true,
                'message'   => 'Status changed successfully.',
                'data'      => $data,
            ]);
        } catch (\Exception $e) {
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
            $page = VideoTutorial::findOrFail($id);
            $page->delete();

            return response()->json([
                't-success' => true,
                'success'   => true,
                'message'   => 'Video tutorial deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                't-error' => true,
                'success' => false,
                'message' => 'Failed to delete video tutorial: ' . $e->getMessage(),
            ], 500);
        }
    }
}
