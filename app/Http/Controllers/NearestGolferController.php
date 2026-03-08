<?php

namespace App\Http\Controllers;

use App\Models\Golfer;
use Illuminate\Http\Request;

class NearestGolferController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $latitude = (float) $validated['latitude'];
        $longitude = (float) $validated['longitude'];

        $golfers = Golfer::query()
            ->select('id', 'debitor_account', 'name', 'email', 'born_at', 'latitude', 'longitude')
            ->selectRaw(
                '(6371 * acos(
                    cos(radians(?)) *
                    cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) *
                    sin(radians(latitude))
                )) as distance',
                [$latitude, $longitude, $latitude]
            )
            ->orderBy('distance')
            ->limit(500)
            ->get();

        return response()->json($golfers);
    }
}