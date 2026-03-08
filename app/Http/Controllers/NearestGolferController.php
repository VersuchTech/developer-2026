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

 public function csv(Request $request)
{
    $latitude = $request->latitude;
    $longitude = $request->longitude;

    $golfers = Golfer::selectRaw("
        *,
        (6371 * acos(
            cos(radians(?)) *
            cos(radians(latitude)) *
            cos(radians(longitude) - radians(?)) +
            sin(radians(?)) *
            sin(radians(latitude))
        )) AS distance
    ", [$latitude, $longitude, $latitude])
    ->orderBy('distance')
    ->limit(500)
    ->get();

    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename=\"nearest-golfers.csv\"',
    ];

    $callback = function () use ($golfers) {
        $file = fopen('php://output', 'w');

        fputcsv($file, [
            'id',
            'debitor_account',
            'name',
            'email',
            'born_at',
            'latitude',
            'longitude',
            'distance'
        ]);

        foreach ($golfers as $golfer) {
            fputcsv($file, [
                $golfer->id,
                $golfer->debitor_account,
                $golfer->name,
                $golfer->email,
                $golfer->born_at,
                $golfer->latitude,
                $golfer->longitude,
                $golfer->distance,
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
}