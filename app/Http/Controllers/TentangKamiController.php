<?php

namespace App\Http\Controllers;

use App\Models\TentangKami;
use Illuminate\Http\Request;

class TentangKamiController extends Controller
{
    /**
     * GET /admin/tentang-kami
     * Returns flat JSON: { hero_title, hero_subtitle, hero_description, layanan:[], statistik:[], kontak_info:{} }
     */
    public function index()
    {
        return response()->json($this->buildFlatResponse());
    }

    /**
     * PUT /admin/tentang-kami
     * Accepts frontend flat JSON: { section: "hero", data: { hero_title, hero_subtitle, ... } }
     */
    public function update(Request $request)
    {
        try {
            $data = $request->input('data', []);

            // If data is a flat object (from frontend), flatten it into DB rows
            if (is_array($data) && !isset($data[0])) {
                $rows = $this->flattenToRows($data);
                foreach ($rows as $row) {
                    TentangKami::updateOrCreate(
                        ['section' => $row['section'], 'section_key' => $row['section_key']],
                        ['content_type' => $row['content_type'], 'content_value' => $row['content_value']]
                    );
                }
            } else {
                // Legacy format: data is already an array of {section, section_key, ...}
                foreach ($data as $item) {
                    if (!is_array($item)) continue;
                    TentangKami::updateOrCreate(
                        ['section' => $item['section'], 'section_key' => $item['section_key']],
                        ['content_type' => $item['content_type'] ?? 'text', 'content_value' => $item['content_value'] ?? '']
                    );
                }
            }

            return response()->json(['message' => 'Tentang kami berhasil diupdate']);
        } catch (\Exception $e) {
            \Log::error('TentangKami update error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['detail' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    public function reset()
    {
        TentangKami::truncate();
        return response()->json(['message' => 'Tentang kami berhasil direset']);
    }

    public function publicIndex()
    {
        return response()->json($this->buildFlatResponse());
    }

    // ============ PRIVATE HELPERS ============

    private function buildFlatResponse(): array
    {
        $rows = TentangKami::all();
        $result = [
            'hero_title' => '',
            'hero_subtitle' => '',
            'hero_description' => '',
            'layanan' => [],
            'statistik' => [],
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
                case 'layanan.items':
                    $decoded = json_decode($value, true);
                    $result['layanan'] = is_array($decoded) ? $decoded : [];
                    break;
                case 'statistik.items':
                    $decoded = json_decode($value, true);
                    $result['statistik'] = is_array($decoded) ? $decoded : [];
                    break;
                case 'kontak_info.data':
                    $decoded = json_decode($value, true);
                    $result['kontak_info'] = is_array($decoded) ? $decoded : new \stdClass();
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
        if (isset($data['hero_description'])) {
            $rows[] = ['section' => 'hero', 'section_key' => 'description', 'content_type' => 'text', 'content_value' => $data['hero_description']];
        }
        if (isset($data['layanan'])) {
            $rows[] = ['section' => 'layanan', 'section_key' => 'items', 'content_type' => 'json', 'content_value' => json_encode($data['layanan'])];
        }
        if (isset($data['statistik'])) {
            $rows[] = ['section' => 'statistik', 'section_key' => 'items', 'content_type' => 'json', 'content_value' => json_encode($data['statistik'])];
        }
        if (isset($data['kontak_info'])) {
            $rows[] = ['section' => 'kontak_info', 'section_key' => 'data', 'content_type' => 'json', 'content_value' => json_encode($data['kontak_info'])];
        }

        return $rows;
    }
}
