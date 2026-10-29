<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DynamicGalleryResource;
use App\Models\DynamicGallery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DynamicGalleryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = DynamicGallery::where('status', 'active');

        // Optional filter by ID if passed
        if ($request->filled('id')) {
            $query->where('id', $request->input('id'));
        }

        // Optional filter by type.
        $type = $request->query('type') ?? $request->input('type') ?? $request->query('view_type');
        if ($type) {
            $typeClean = str_contains(strtolower($type), 'side') ? 'side' : 'front';
            $query->where('type', $typeClean);
        }

        $galleries = $query->latest('id')->get();
        return response()->json([
            'success' => true,
            'message' => 'Dynamic gallery items fetched successfully',
            'code'    => 200,
            'data'    => DynamicGalleryResource::collection($galleries),
        ], 200);
    }

    public function show(Request $request, string|int $identifier = null): JsonResponse
    {
        $id   = $request->input('id') ?? $request->query('id');
        $type = $request->input('type') ?? $request->query('type') ?? $request->query('view_type');

        // Check if identifier was passed directly in the URL route parameter (/dynamic-galleries/{id})
        if ($identifier !== null && $identifier !== '') {
            if (is_numeric($identifier)) {
                $id = $id ?? (int) $identifier;
            } elseif (in_array(strtolower($identifier), ['front', 'side', 'front_view', 'side_view'])) {
                $type = $type ?? (str_contains(strtolower($identifier), 'side') ? 'side' : 'front');
            } else {
                $type = $type ?? strtolower($identifier);
            }
        }

        $query = DynamicGallery::where('status', 'active');
        $gallery = null;

        // Case 1: Both id and type are provided
        if ($id && $type) {
            $typeClean = str_contains(strtolower($type), 'side') ? 'side' : 'front';
            $gallery = (clone $query)->where('id', $id)->where('type', $typeClean)->first();

            // Fallback: if not matching both simultaneously, find by id or by type
            if (!$gallery) {
                $gallery = (clone $query)->where('id', $id)->first()
                        ?? (clone $query)->where('type', $typeClean)->first();
            }
        }
        // Case 2: Only id is provided
        elseif ($id) {
            $gallery = $query->where('id', $id)->first();
        }
        // Case 3: Only type is provided ('front' or 'side')
        elseif ($type) {
            $typeClean = str_contains(strtolower($type), 'side') ? 'side' : 'front';
            $gallery = $query->where('type', $typeClean)->first();
        }
        // Case 4: Neither provided
        else {
            return response()->json([
                'success' => false,
                'message' => 'Please provide an id or type (front / side)',
                'code'    => 400,
                'data'    => null,
            ], 400);
        }

        if (!$gallery) {
            return response()->json([
                'success' => false,
                'message' => 'Dynamic gallery item not found',
                'code'    => 404,
                'data'    => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Dynamic gallery item fetched successfully',
            'code'    => 200,
            'data'    => new DynamicGalleryResource($gallery),
        ], 200);
    }
}
