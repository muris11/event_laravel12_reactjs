<?php

namespace App\Http\Controllers;

use App\Models\EventSlider;
use App\Models\TentangKamiSlider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SliderController extends Controller
{
    // ========== EVENT SLIDER ==========
    public function index()
    {
        $sliders = EventSlider::orderBy('order_position')->get();
        return response()->json($sliders);
    }

    public function store(Request $request)
    {
        try {
            $file = $request->file('image');
            if (!$file) return response()->json(['detail' => 'File gambar wajib diupload'], 400);

            $filename = 'slider_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('uploads/slider', $filename, 'public');

            [$width, $height] = $this->getImageDimensions(storage_path('app/public/uploads/slider/' . $filename));
            $orientasi = $width > $height ? 'landscape' : ($width < $height ? 'portrait' : 'square');

            $slider = EventSlider::create([
                'image_url' => '/uploads/slider/' . $filename,
                'description' => $request->input('description', ''),
                'order_position' => $request->input('order_position', EventSlider::max('order_position') + 1),
                'is_active' => true,
                'orientasi' => $orientasi,
                'width' => $width,
                'height' => $height,
                'crop_mode' => $request->input('crop_mode', 'smart'),
            ]);

            return response()->json(['message' => 'Slider berhasil ditambahkan', 'slider' => $slider], 201);
        } catch (\Exception $e) {
            return response()->json(['detail' => 'Gagal menambahkan slider: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $slider = EventSlider::find($id);
        if (!$slider) return response()->json(['detail' => 'Slider tidak ditemukan'], 404);

        if ($request->hasFile('image')) {
            $this->deleteSliderImage($slider->image_url);
            $file = $request->file('image');
            $filename = 'slider_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('uploads/slider', $filename, 'public');

            [$width, $height] = $this->getImageDimensions(storage_path('app/public/uploads/slider/' . $filename));
            $slider->image_url = '/uploads/slider/' . $filename;
            $slider->orientasi = $width > $height ? 'landscape' : ($width < $height ? 'portrait' : 'square');
            $slider->width = $width;
            $slider->height = $height;
        }

        if ($request->has('description')) $slider->description = $request->input('description');
        if ($request->has('order_position')) $slider->order_position = $request->input('order_position');
        if ($request->has('is_active')) $slider->is_active = filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN);
        if ($request->has('crop_mode')) $slider->crop_mode = $request->input('crop_mode');

        $slider->save();
        return response()->json(['message' => 'Slider berhasil diupdate', 'slider' => $slider]);
    }

    public function reprocess($id)
    {
        $slider = EventSlider::find($id);
        if (!$slider) return response()->json(['detail' => 'Slider tidak ditemukan'], 404);

        $path = storage_path('app/public/' . ltrim($slider->image_url, '/'));
        if (file_exists($path)) {
            [$width, $height] = $this->getImageDimensions($path);
            $slider->update([
                'orientasi' => $width > $height ? 'landscape' : ($width < $height ? 'portrait' : 'square'),
                'width' => $width,
                'height' => $height,
            ]);
        }

        return response()->json(['message' => 'Slider berhasil diproses ulang', 'slider' => $slider->fresh()]);
    }

    public function destroy($id)
    {
        $slider = EventSlider::find($id);
        if (!$slider) return response()->json(['detail' => 'Slider tidak ditemukan'], 404);

        $this->deleteSliderImage($slider->image_url);
        $slider->delete();
        return response()->json(['message' => 'Slider berhasil dihapus']);
    }

    public function stats()
    {
        return response()->json([
            'total' => EventSlider::count(),
            'active' => EventSlider::where('is_active', true)->count(),
            'landscape' => EventSlider::where('orientasi', 'landscape')->count(),
            'portrait' => EventSlider::where('orientasi', 'portrait')->count(),
        ]);
    }

    public function publicIndex()
    {
        $sliders = EventSlider::where('is_active', true)->orderBy('order_position')->get();
        return response()->json($sliders);
    }

    // ========== TENTANG KAMI SLIDER ==========
    public function tentangKamiIndex()
    {
        $sliders = TentangKamiSlider::orderBy('order_position')->get();
        return response()->json($sliders);
    }

    public function tentangKamiStore(Request $request)
    {
        try {
            $file = $request->file('image');
            if (!$file) return response()->json(['detail' => 'File gambar wajib diupload'], 400);

            $filename = 'tk_slider_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('uploads/tentang_kami', $filename, 'public');

            [$width, $height] = $this->getImageDimensions(storage_path('app/public/uploads/tentang_kami/' . $filename));
            $orientasi = $width > $height ? 'landscape' : ($width < $height ? 'portrait' : 'square');

            $slider = TentangKamiSlider::create([
                'image_url' => '/uploads/tentang_kami/' . $filename,
                'description' => $request->input('description', ''),
                'order_position' => $request->input('order_position', TentangKamiSlider::max('order_position') + 1),
                'is_active' => true,
                'orientasi' => $orientasi,
                'width' => $width,
                'height' => $height,
                'crop_mode' => $request->input('crop_mode', 'smart'),
            ]);

            return response()->json(['message' => 'Tentang kami slider berhasil ditambahkan', 'slider' => $slider], 201);
        } catch (\Exception $e) {
            return response()->json(['detail' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    public function tentangKamiDestroy($id)
    {
        $slider = TentangKamiSlider::find($id);
        if (!$slider) return response()->json(['detail' => 'Slider tidak ditemukan'], 404);

        $this->deleteSliderImage($slider->image_url);
        $slider->delete();
        return response()->json(['message' => 'Slider berhasil dihapus']);
    }

    public function tentangKamiPublic()
    {
        $sliders = TentangKamiSlider::where('is_active', true)->orderBy('order_position')->get();
        return response()->json($sliders);
    }

    // ========== HELPERS ==========
    protected function getImageDimensions(string $path): array
    {
        if (!file_exists($path)) return [0, 0];
        $info = @getimagesize($path);
        return $info ? [$info[0], $info[1]] : [0, 0];
    }

    protected function deleteSliderImage(?string $imageUrl): void
    {
        if (!$imageUrl) return;
        $path = ltrim($imageUrl, '/');
        Storage::disk('public')->delete($path);
    }
}
