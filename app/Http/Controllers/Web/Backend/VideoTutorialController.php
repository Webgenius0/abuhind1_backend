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
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class VideoTutorialController extends Controller
{
    public function index(Request $request): View | JsonResponse
    {
        if ($request->ajax()) {
            $data = VideoTutorial::latest();
            if (!empty($request->input('search.value'))) {
                $searchTerm = $request->input('search.value');
                $data->where('title', 'LIKE', "%$searchTerm%");
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('description', function ($data) {
                    $desc = $data->description;
                    $short_desc = strlen($desc) > 100 ? substr($desc, 0, 100) . '...' : $desc;
                    return '<p>' . $short_desc . '</p>';
                })
                ->addColumn('youtube_link', function ($data) {
                    return '<a href="' . $data->youtube_link . '" target="_blank">Watch Video</a>';
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
                                <a href="' . route('admin.video_tutorial.edit', ['id' => $data->id]) . '" class="text-white btn btn-primary" title="Edit">
                                   <i class="fa fa-pencil" aria-hidden="true"></i>
                                </a>
                            </div>';
                })
                ->rawColumns(['description', 'youtube_link', 'status', 'action'])
                ->make();
        }
        return view('backend.layouts.video_tutorial.index');
    }

    public function create(): View | RedirectResponse
    {
        if (User::find(auth()->user()->id)) {
            return view('backend.layouts.video_tutorial.create');
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
            return view('backend.layouts.video_tutorial.edit', compact('data'));
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
        $data = VideoTutorial::findOrFail($id);
        if ($data->status == 'active') {
            $data->status = 'inactive';
            $data->save();

            return response()->json([
                'success' => false,
                'message' => 'Unpublished Successfully.',
                'data'    => $data,
            ]);
        } else {
            $data->status = 'active';
            $data->save();

            return response()->json([
                'success' => true,
                'message' => 'Published Successfully.',
                'data'    => $data,
            ]);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        $page = VideoTutorial::findOrFail($id);
        $page->delete();
        return response()->json([
            't-success' => true,
            'message'   => 'Deleted successfully.',
        ]);
    }
}
