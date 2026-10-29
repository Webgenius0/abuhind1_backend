<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\DynamicPage;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class DynamicPageController extends Controller
{
    /**
     * Display a paginated listing of the dynamic pages via Yajra DataTables (AJAX) or view (Web).
     */
    public function index(Request $request): View | JsonResponse
    {
        if ($request->ajax()) {
            $query = DynamicPage::query()->latest('id');

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('page_title', function (DynamicPage $page) {
                    return ! empty($page->page_title) ? e($page->page_title) : 'N/A';
                })
                ->editColumn('page_slug', function (DynamicPage $page) {
                    return '<code class="px-2 py-0.5 text-xs font-mono rounded bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">/' . e($page->page_slug) . '</code>';
                })
                ->editColumn('page_content', function (DynamicPage $page) {
                    return e(Str::limit(strip_tags((string) $page->page_content), 65) ?: 'N/A');
                })
                ->addColumn('status', function (DynamicPage $page) {
                    $checked = ($page->status ?? 'active') === 'active' ? 'checked' : '';

                    return '<label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" onclick="changeStatus(event, ' . $page->id . ')"
                            class="sr-only peer" ' . $checked . '>
                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[\'\'] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-green-500"></div>
                    </label>';
                })
                ->addColumn('created_at', function (DynamicPage $page) {
                    return $page->created_at ? $page->created_at->format('d M, Y') : 'N/A';
                })
                ->addColumn('action', function (DynamicPage $page) {
                    $viewUrl = route('page.show', $page->page_slug);
                    $editUrl = route('admin.dynamic_page.edit', $page->id);

                    return '<div class="flex items-center justify-center gap-2">
                        <a href="' . $viewUrl . '" target="_blank"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-emerald-600 bg-emerald-100 hover:bg-emerald-600 hover:text-white dark:text-emerald-400 dark:bg-emerald-900/30 dark:hover:bg-emerald-600 dark:hover:text-white transition-all shadow-sm hover:shadow"
                            title="View Page">
                            <i class="fa-solid fa-eye text-sm"></i>
                        </a>
                        <a href="' . $editUrl . '"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-blue-600 bg-blue-100 hover:bg-blue-600 hover:text-white dark:text-blue-400 dark:bg-blue-900/30 dark:hover:bg-blue-600 dark:hover:text-white transition-all shadow-sm hover:shadow"
                            title="Edit Page">
                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                        </a>
                        <button type="button"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-red-600 bg-red-100 hover:bg-red-600 hover:text-white dark:text-red-400 dark:bg-red-900/30 dark:hover:bg-red-600 dark:hover:text-white transition-all shadow-sm hover:shadow"
                            onclick="deleteRecord(event, ' . $page->id . ')"
                            title="Delete Page">
                            <i class="fa-solid fa-trash-can text-sm"></i>
                        </button>
                    </div>';
                })
                ->rawColumns(['page_slug', 'status', 'action'])
                ->make(true);
        }

        return view('backend.v2.pages.dynamic_page.index');
    }

    /**
     * Show the form for creating a new dynamic page.
     *
     * @return View|RedirectResponse
     */
    public function create(): View | RedirectResponse
    {
        if (User::find(auth()->user()->id)) {
            return view('backend.v2.pages.dynamic_page.create');
        }
        return redirect()->route('admin.dynamic_page.index');
    }

    /**
     * Store a newly created dynamic page in the database.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            if (User::find(auth()->user()->id)) {
                $validator = Validator::make($request->all(), [
                    'page_title'   => 'required|string',
                    'page_content' => 'required|string',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                $data               = new DynamicPage();
                $data->page_title   = $request->page_title;
                $data->page_slug    = Str::slug($request->page_title);
                $data->page_content = $request->page_content;
                $data->save();
            }
            return redirect()->route('admin.dynamic_page.index')->with('t-success', 'Dynamic Page created successfully.');
        } catch (Exception) {
            return redirect()->route('admin.dynamic_page.index')->with('t-error', 'Dynamic Page failed created.');
        }
    }

    /**
     * Show the form for editing the specified dynamic page.
     *
     * @param int $id
     * @return View|RedirectResponse
     */
    public function edit(int $id): View | RedirectResponse
    {
        if (User::find(auth()->user()->id)) {
            $data = DynamicPage::findOrFail($id);
            return view('backend.v2.pages.dynamic_page.edit', compact('data'));
        }
        return redirect()->route('admin.dynamic_page.index');
    }

    /**
     * Update the specified dynamic page in the database.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        try {
            if (User::find(auth()->user()->id)) {
                $validator = Validator::make($request->all(), [
                    'page_title'   => 'nullable|string',
                    'page_content' => 'nullable|string',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                $data = DynamicPage::findOrFail($id);
                $data->update([
                    'page_title'   => $request->page_title,
                    'page_slug'    => Str::slug($request->page_title),
                    'page_content' => $request->page_content,
                ]);

                return redirect()->route('admin.dynamic_page.index')->with('t-success', 'Dynamic Page Updated Successfully.');
            }
        } catch (Exception) {
            return redirect()->route('admin.dynamic_page.index')->with('t-error', 'Dynamic Page failed to update');
        }
        return redirect()->route('admin.dynamic_page.index');
    }

    /**
     * Change the status of the specified dynamic page.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function status(int $id): JsonResponse
    {
        try {
            $data = DynamicPage::findOrFail($id);
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

    /**
     * Remove the specified dynamic page from the database.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $page = DynamicPage::findOrFail($id);
            $page->delete();

            return response()->json([
                't-success' => true,
                'success'   => true,
                'message'   => 'Dynamic page deleted successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                't-error' => true,
                'success' => false,
                'message' => 'Failed to delete page: ' . $e->getMessage(),
            ], 500);
        }
    }
}
