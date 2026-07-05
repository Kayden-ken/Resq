<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    /**
     * Get all facilities
     */
    public function index(Request $request)
    {
        $query = Facility::query();

        // Filter by type
        if ($request->type) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $facilities = $query->orderBy('name')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $facilities
        ]);
    }

    /**
     * Get single facility
     */
    public function show(Request $request, $id)
    {
        $facility = Facility::find($id);

        if (!$facility) {
            return response()->json([
                'success' => false,
                'message' => 'Facility not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $facility
        ]);
    }

    /**
     * Get nearby facilities
     */
    public function nearby(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius' => ['nullable', 'numeric', 'min:1', 'max:100'],
            'type' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $radius = $request->radius ?? 50; // km
        $type = $request->type;

        $query = Facility::selectRaw(
            "*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance",
            [$latitude, $longitude, $latitude]
        )
            ->where('is_active', true);

        if ($type) {
            $query->where('type', $type);
        }

        $facilities = $query->having('distance', '<', $radius)
            ->orderBy('distance')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $facilities
        ]);
    }
}