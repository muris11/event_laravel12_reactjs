<?php

namespace App\Http\Controllers;

use App\Models\SliderEvent;
use Illuminate\Http\Request;

class SliderEventsController extends Controller
{
    public function index()
    {
        $sliderEvent = SliderEvent::latest()->first();
        return response()->json($sliderEvent ? $sliderEvent->selected_events : []);
    }

    public function store(Request $request)
    {
        try {
            $selectedEvents = $request->input('selected_events', []);

            // Delete old entries and create new
            SliderEvent::truncate();
            $sliderEvent = SliderEvent::create(['selected_events' => $selectedEvents]);

            return response()->json([
                'message' => 'Slider events berhasil disimpan',
                'selected_events' => $sliderEvent->selected_events,
            ]);
        } catch (\Exception $e) {
            return response()->json(['detail' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    public function publicIndex()
    {
        $sliderEvent = SliderEvent::latest()->first();
        return response()->json($sliderEvent ? $sliderEvent->selected_events : []);
    }
}
