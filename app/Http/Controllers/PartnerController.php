<?php

namespace App\Http\Controllers;

use App\Models\PartnerContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PartnerController extends Controller
{
    /**
     * GET /admin/partner
     * Returns flat JSON: { hero_title, hero_subtitle, partners:[] }
     */
    public function index()
    {
        return response()->json($this->buildFlatResponse());
    }

    /**
     * PUT /admin/partner
     * Accepts frontend flat JSON: { section: "hero", data: { hero_title, hero_subtitle, partners:[...] } }
     */
    public function update(Request $request)
    {
        try {
            $data = $request->input('data', []);

            // If data is a flat object (from frontend), flatten it into DB rows
            if (is_array($data) && !isset($data[0])) {
                $rows = $this->flattenToRows($data);
                foreach ($rows as $row) {
                    PartnerContent::updateOrCreate(
                        ['section' => $row['section'], 'section_key' => $row['section_key']],
                        ['content_type' => $row['content_type'], 'content_value' => $row['content_value']]
                    );
                }
            } else {
                // Legacy format: data is already an array of {section, section_key, ...}
                foreach ($data as $item) {
                    if (!is_array($item)) continue;
                    PartnerContent::updateOrCreate(
                        ['section' => $item['section'], 'section_key' => $item['section_key']],
                        ['content_type' => $item['content_type'] ?? 'text', 'content_value' => $item['content_value'] ?? '']
                    );
                }
            }

            return response()->json(['message' => 'Partner berhasil diupdate']);
        } catch (\Exception $e) {
            \Log::error('Partner update error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['detail' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    public function reset()
    {
        PartnerContent::truncate();
        return response()->json(['message' => 'Partner berhasil direset']);
    }

    public function uploadImage(Request $request)
    {
        try {
            $file = $request->file('image') ?? $request->file('file');
            if (!$file) return response()->json(['detail' => 'File gambar wajib diupload'], 400);

            $filename = 'partner_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('static/uploads/partner', $filename, 'public');

            return response()->json([
                'url' => '/static/uploads/partner/' . $filename,
                'image_url' => '/static/uploads/partner/' . $filename,
                'message' => 'Image berhasil diupload',
            ]);
        } catch (\Exception $e) {
            return response()->json(['detail' => 'Gagal upload: ' . $e->getMessage()], 500);
        }
    }

    public function debug()
    {
        $path = storage_path('app/public/static/uploads/partner');
        $files = is_dir($path) ? array_diff(scandir($path), ['.', '..']) : [];
        return response()->json([
            'path' => $path,
            'exists' => is_dir($path),
            'files' => array_values($files),
            'count' => count($files),
        ]);
    }

    public function publicIndex()
    {
        return response()->json($this->buildFlatResponse());
    }

    // ============ PRIVATE HELPERS ============

    private function buildFlatResponse(): array
    {
        $rows = PartnerContent::all();
        $result = [
            'hero_title' => '',
            'hero_subtitle' => '',
            'partners' => [],
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
                case 'partners.items':
                    $decoded = json_decode($value, true);
                    $result['partners'] = is_array($decoded) ? $decoded : [];
                    break;
            }
        }

        return $result;
    }

    private function flattenToRows(array $data): array
    {
        $rows = [];

        if (isset($data['hero_title'])) {
            $rows[] = ['section' => 'hero', 'section_key' => 'title', 'content_type' => 'text', 'content_value' => $data['hero_title']];
        }
        if (isset($data['hero_subtitle'])) {
            $rows[] = ['section' => 'hero', 'section_key' => 'subtitle', 'content_type' => 'text', 'content_value' => $data['hero_subtitle']];
        }
        if (isset($data['partners'])) {
            $rows[] = ['section' => 'partners', 'section_key' => 'items', 'content_type' => 'json', 'content_value' => json_encode($data['partners'])];
        }

        return $rows;
    }
}
