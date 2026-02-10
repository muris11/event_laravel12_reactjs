<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Category;
use App\Models\TiketKategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $kelas = Kelas::with('category')->orderBy('created_at', 'desc')->get();
        $result = $kelas->map(fn($k) => $this->formatKelas($k));
        return response()->json($result);
    }

    public function show($id)
    {
        $kelas = Kelas::with(['category', 'tiketKategori'])->find($id);
        if (!$kelas) return response()->json(['detail' => 'Kelas tidak ditemukan'], 404);
        return response()->json($this->formatKelas($kelas));
    }

    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $foto = null;

            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $filename = 'kelas_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
                $file->storeAs('uploads/kelas', $filename, 'public');
                $foto = '/uploads/kelas/' . $filename;
            }

            // Resolve kategori name to kategori_id if needed
            $kategoriId = $data['kategori_id'] ?? null;
            if (!$kategoriId && isset($data['kategori']) && $data['kategori']) {
                $category = Category::where('nama', $data['kategori'])->first();
                if ($category) {
                    $kategoriId = $category->id;
                }
            }

            // Handle gambaran_event as multiple file uploads
            $gambaranPaths = [];
            if ($request->hasFile('gambaran_event')) {
                $files = $request->file('gambaran_event');
                // Handle both single and multiple file uploads
                if (!is_array($files)) $files = [$files];
                foreach ($files as $file) {
                    $filename = 'gambaran_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('uploads/kelas', $filename, 'public');
                    $gambaranPaths[] = '/uploads/kelas/' . $filename;
                }
            }

            $kelas = Kelas::create([
                'nama_kelas' => $data['nama_kelas'] ?? '',
                'kategori_id' => $kategoriId,
                'deskripsi' => $data['deskripsi'] ?? null,
                'jadwal' => $data['jadwal'] ?? null,
                'ruangan' => $data['ruangan'] ?? null,
                'biaya' => $data['biaya'] ?? 0,
                'total_peserta' => $data['total_peserta'] ?? 0,
                'foto' => $foto,
                'gambaran_event' => !empty($gambaranPaths) ? json_encode($gambaranPaths) : ($data['gambaran_event'] ?? null),
                'link_navigasi' => $data['link_navigasi'] ?? '',
                'is_link_eksternal' => filter_var($data['is_link_eksternal'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ]);

            return response()->json([
                'message' => 'Kelas berhasil ditambahkan',
                'kelas_id' => $kelas->id,
                'kelas' => $this->formatKelas($kelas->load('category')),
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['detail' => 'Gagal menambahkan kelas: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $kelas = Kelas::find($id);
            if (!$kelas) return response()->json(['detail' => 'Kelas tidak ditemukan'], 404);

            $data = $request->all();

            if ($request->hasFile('foto')) {
                // Delete old photo
                if ($kelas->foto) {
                    $oldPath = ltrim($kelas->foto, '/');
                    Storage::disk('public')->delete($oldPath);
                }
                $file = $request->file('foto');
                $filename = 'kelas_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
                $file->storeAs('uploads/kelas', $filename, 'public');
                $data['foto'] = '/uploads/kelas/' . $filename;
            }

            // Resolve kategori name to kategori_id if needed
            if (!isset($data['kategori_id']) && isset($data['kategori']) && $data['kategori']) {
                $category = Category::where('nama', $data['kategori'])->first();
                if ($category) {
                    $data['kategori_id'] = $category->id;
                }
                unset($data['kategori']);
            }

            if (isset($data['is_link_eksternal'])) {
                $data['is_link_eksternal'] = filter_var($data['is_link_eksternal'], FILTER_VALIDATE_BOOLEAN);
            }

            $kelas->update(array_filter($data, fn($v) => $v !== null));
            return response()->json(['message' => 'Kelas berhasil diupdate', 'kelas' => $this->formatKelas($kelas->fresh()->load('category'))]);
        } catch (\Exception $e) {
            return response()->json(['detail' => 'Gagal update kelas: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $kelas = Kelas::find($id);
        if (!$kelas) return response()->json(['detail' => 'Kelas tidak ditemukan'], 404);

        if ($kelas->foto) {
            $path = ltrim($kelas->foto, '/');
            Storage::disk('public')->delete($path);
        }

        $kelas->delete();
        return response()->json(['message' => 'Kelas berhasil dihapus']);
    }

    public function getPublicList()
    {
        $kelas = Kelas::with('category')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($k) => $this->formatKelas($k));

        return response()->json($kelas);
    }

    public function getPublicDetail($id)
    {
        $kelas = Kelas::with(['category', 'tiketKategori' => function ($q) {
            $q->where('is_active', true);
        }])->find($id);

        if (!$kelas) return response()->json(['detail' => 'Kelas tidak ditemukan'], 404);
        return response()->json($this->formatKelas($kelas));
    }

    public function getImage($id)
    {
        $kelas = Kelas::find($id);
        if (!$kelas || !$kelas->foto) return response()->json(['detail' => 'Image not found'], 404);

        $path = storage_path('app/public/' . ltrim($kelas->foto, '/'));
        if (!file_exists($path)) return response()->json(['detail' => 'File not found'], 404);

        return response()->file($path);
    }

    public function getTiketKategori($id)
    {
        $tiket = TiketKategori::where('kelas_id', $id)->get();
        return response()->json($tiket);
    }

    protected function formatKelas($kelas): array
    {
        return [
            'id' => $kelas->id,
            'nama_kelas' => $kelas->nama_kelas,
            'kategori_id' => $kelas->kategori_id,
            'kategori_nama' => $kelas->category?->nama ?? null,
            'deskripsi' => $kelas->deskripsi,
            'jadwal' => $kelas->jadwal,
            'ruangan' => $kelas->ruangan,
            'biaya' => (float) $kelas->biaya,
            'total_peserta' => $kelas->total_peserta,
            'foto' => $kelas->foto ? ltrim(str_replace('/uploads/', '', $kelas->foto), '/') : null,
            'gambaran_event' => $kelas->gambaran_event,
            'link_navigasi' => $kelas->link_navigasi,
            'is_link_eksternal' => (bool) $kelas->is_link_eksternal,
            'created_at' => $kelas->created_at?->toIso8601String(),
            'updated_at' => $kelas->updated_at?->toIso8601String(),
            'tiket_kategori' => $kelas->relationLoaded('tiketKategori') ? $kelas->tiketKategori : null,
        ];
    }
}
