<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\LayananSlider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LayananController extends Controller
{
    /**
     * GET /admin/layanan
     * Returns flat JSON that the frontend expects:
     * { hero_title, hero_subtitle, hero_description, services:[], target_audience:[], kontak_info:{} }
     */
    public function index()
    {
        return response()->json($this->buildFlatResponse());
    }

    /**
     * PUT /admin/layanan
     * Accepts frontend flat JSON: { section: "hero", data: { hero_title, hero_subtitle, ... } }
     * Flattens it into DB rows: { section, section_key, content_type, content_value }
     */
    public function update(Request $request)
    {
        try {
            $data = $request->input('data', []);

            // If data is a flat object (from frontend), flatten it into DB rows
            if (is_array($data) && !isset($data[0])) {
                $rows = $this->flattenToRows($data);
                foreach ($rows as $row) {
                    Layanan::updateOrCreate(
                        ['section' => $row['section'], 'section_key' => $row['section_key']],
                        ['content_type' => $row['content_type'], 'content_value' => $row['content_value']]
                    );
                }
            } else {
                // Legacy format: data is already an array of {section, section_key, ...}
                foreach ($data as $item) {
                    if (!is_array($item)) continue;
                    Layanan::updateOrCreate(
                        ['section' => $item['section'], 'section_key' => $item['section_key']],
                        ['content_type' => $item['content_type'] ?? 'text', 'content_value' => $item['content_value'] ?? '']
                    );
                }
            }

            return response()->json(['message' => 'Layanan berhasil diupdate']);
        } catch (\Exception $e) {
            \Log::error('Layanan update error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['detail' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    public function reset()
    {
        Layanan::truncate();
        return response()->json(['message' => 'Layanan berhasil direset']);
    }

    // ============ SLIDER METHODS ============

    public function sliderIndex()
    {
        $sliders = LayananSlider::orderBy('order_position')->get();
        return response()->json($sliders);
    }

    public function sliderStore(Request $request)
    {
        try {
            $file = $request->file('image');
            if (!$file) return response()->json(['detail' => 'File gambar wajib diupload'], 400);

            $filename = 'layanan_slider_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('uploads/layanan', $filename, 'public');

            $fullPath = storage_path('app/public/uploads/layanan/' . $filename);
            [$width, $height] = $this->getImageDimensions($fullPath);
            $orientasi = $width > $height ? 'landscape' : ($width < $height ? 'portrait' : 'square');

            $slider = LayananSlider::create([
                'image_url' => '/uploads/layanan/' . $filename,
                'description' => $request->input('description', ''),
                'order_position' => $request->input('order_position', LayananSlider::max('order_position') + 1),
                'is_active' => true,
                'orientasi' => $orientasi,
                'width' => $width,
                'height' => $height,
                'crop_mode' => $request->input('crop_mode', 'smart'),
            ]);

            return response()->json(['message' => 'Slider layanan berhasil ditambahkan', 'slider' => $slider], 201);
        } catch (\Exception $e) {
            return response()->json(['detail' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    public function sliderUpdate(Request $request, $id)
    {
        $slider = LayananSlider::find($id);
        if (!$slider) return response()->json(['detail' => 'Slider tidak ditemukan'], 404);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete(ltrim($slider->image_url, '/'));
            $file = $request->file('image');
            $filename = 'layanan_slider_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('uploads/layanan', $filename, 'public');

            $fullPath = storage_path('app/public/uploads/layanan/' . $filename);
            [$width, $height] = $this->getImageDimensions($fullPath);
            $slider->image_url = '/uploads/layanan/' . $filename;
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

    public function sliderDestroy($id)
    {
        $slider = LayananSlider::find($id);
        if (!$slider) return response()->json(['detail' => 'Slider tidak ditemukan'], 404);

        Storage::disk('public')->delete(ltrim($slider->image_url, '/'));
        $slider->delete();
        return response()->json(['message' => 'Slider berhasil dihapus']);
    }

    public function stats()
    {
        return response()->json([
            'total_content' => Layanan::count(),
            'total_sliders' => LayananSlider::count(),
            'active_sliders' => LayananSlider::where('is_active', true)->count(),
        ]);
    }

    public function bulkOperations(Request $request)
    {
        try {
            $operations = $request->input('operations', []);
            $results = [];
            foreach ($operations as $op) {
                $action = $op['action'] ?? '';
                if ($action === 'update') {
                    Layanan::updateOrCreate(
                        ['section' => $op['section'], 'section_key' => $op['section_key']],
                        ['content_type' => $op['content_type'] ?? 'text', 'content_value' => $op['content_value'] ?? '']
                    );
                    $results[] = ['action' => 'update', 'status' => 'success'];
                } elseif ($action === 'delete') {
                    Layanan::where('section', $op['section'])->where('section_key', $op['section_key'])->delete();
                    $results[] = ['action' => 'delete', 'status' => 'success'];
                }
            }
            return response()->json(['message' => 'Bulk operations selesai', 'results' => $results]);
        } catch (\Exception $e) {
            return response()->json(['detail' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    public function publicIndex()
    {
        return response()->json($this->buildFlatResponse());
    }

    public function sliderPublic()
    {
        $sliders = LayananSlider::where('is_active', true)->orderBy('order_position')->get();
        return response()->json($sliders);
    }

    // ============ PRIVATE HELPERS ============

    /**
     * Build flat JSON response from DB rows for the frontend.
     */
    private function buildFlatResponse(): array
    {
        $rows = Layanan::all();
        $result = [
            'hero_title' => '',
            'hero_subtitle' => '',
            'hero_description' => '',
            'services' => [],
            'target_audience' => [],
            'kontak_info' => new \stdClass(),
        ];

        foreach ($rows as $row) {
            $key = $row->section . '.' . $row->section_key;
            $value = $row->content_value;

            switch ($key) {
                case 'hero.title':
                    $result['hero_title'] = $value;
                    break;
                case 'hero.subtitle':
                    $result['hero_subtitle'] = $value;
                    break;
                case 'hero.description':
                    $result['hero_description'] = $value;
                    break;
                case 'services.items':
                    $decoded = json_decode($value, true);
                    $result['services'] = is_array($decoded) ? $decoded : [];
                    break;
                case 'target_audience.items':
                    $decoded = json_decode($value, true);
                    $result['target_audience'] = is_array($decoded) ? $decoded : [];
                    break;
                case 'kontak_info.data':
                    $decoded = json_decode($value, true);
                    $result['kontak_info'] = is_array($decoded) ? $decoded : new \stdClass();
                    break;
            }
        }

        return $result;
    }

    /**
     * Flatten frontend JSON into DB rows.
     */
    private function flattenToRows(array $data): array
    {
        $rows = [];

        if (isset($data['hero_title'])) {
            $rows[] = ['section' => 'hero', 'section_key' => 'title', 'content_type' => 'text', 'content_value' => $data['hero_title']];
        }
        if (isset($data['hero_subtitle'])) {
            $rows[] = ['section' => 'hero', 'section_key' => 'subtitle', 'content_type' => 'text', 'content_value' => $data['hero_subtitle']];
        }
        if (isset($data['hero_description'])) {
            $rows[] = ['section' => 'hero', 'section_key' => 'description', 'content_type' => 'text', 'content_value' => $data['hero_description']];
        }
        if (isset($data['services'])) {
            $rows[] = ['section' => 'services', 'section_key' => 'items', 'content_type' => 'json', 'content_value' => json_encode($data['services'])];
        }
        if (isset($data['target_audience'])) {
            $rows[] = ['section' => 'target_audience', 'section_key' => 'items', 'content_type' => 'json', 'content_value' => json_encode($data['target_audience'])];
        }
        if (isset($data['kontak_info'])) {
            $rows[] = ['section' => 'kontak_info', 'section_key' => 'data', 'content_type' => 'json', 'content_value' => json_encode($data['kontak_info'])];
        }

        return $rows;
    }

    protected function getImageDimensions(string $path): array
    {
        if (!file_exists($path)) return [0, 0];
        $info = @getimagesize($path);
        return $info ? [$info[0], $info[1]] : [0, 0];
    }
}
