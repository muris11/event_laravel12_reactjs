<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KategoriController extends Controller
{
    public function index()
    {
        $categories = Category::all()->pluck('nama');
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $kategoriNama = $data['kategori'] ?? $data['nama'] ?? null;

            if (!$kategoriNama) {
                return response()->json(['detail' => 'Nama kategori wajib diisi'], 400);
            }

            $category = Category::firstOrCreate(['nama' => $kategoriNama]);

            // Jika ada data kelas sekaligus
            if (isset($data['nama_kelas'])) {
                $foto = null;
                if ($request->hasFile('foto')) {
                    $file = $request->file('foto');
                    $filename = 'kelas_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('uploads/kelas', $filename, 'public');
                    $foto = '/uploads/kelas/' . $filename;
                }

                $kelas = Kelas::create([
                    'nama_kelas' => $data['nama_kelas'],
                    'kategori_id' => $category->id,
                    'deskripsi' => $data['deskripsi'] ?? null,
                    'jadwal' => $data['jadwal'] ?? null,
                    'ruangan' => $data['ruangan'] ?? null,
                    'biaya' => $data['biaya'] ?? 0,
                    'total_peserta' => $data['total_peserta'] ?? 0,
                    'foto' => $foto,
                    'gambaran_event' => $data['gambaran_event'] ?? null,
                    'link_navigasi' => $data['link_navigasi'] ?? '',
                    'is_link_eksternal' => filter_var($data['is_link_eksternal'] ?? false, FILTER_VALIDATE_BOOLEAN),
                ]);
            }

            return response()->json([
                'message' => 'Kategori berhasil ditambahkan',
                'kategori' => $category,
                'kelas' => $kelas ?? null,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['detail' => 'Gagal menambahkan kategori: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($nama)
    {
        $category = Category::where('nama', $nama)->first();
        if (!$category) {
            return response()->json(['detail' => 'Kategori tidak ditemukan'], 404);
        }

        $kelasCount = Kelas::where('kategori_id', $category->id)->count();
        if ($kelasCount > 0) {
            return response()->json(['detail' => "Kategori masih digunakan oleh {$kelasCount} kelas"], 400);
        }

        $category->delete();
        return response()->json(['message' => 'Kategori berhasil dihapus']);
    }
}
