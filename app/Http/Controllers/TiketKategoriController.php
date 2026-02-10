<?php

namespace App\Http\Controllers;

use App\Models\TiketKategori;
use Illuminate\Http\Request;

class TiketKategoriController extends Controller
{
    public function getByKelas($kelasId)
    {
        $tiket = TiketKategori::where('kelas_id', $kelasId)->orderBy('created_at')->get();
        return response()->json($tiket);
    }

    public function show($id)
    {
        $tiket = TiketKategori::find($id);
        if (!$tiket) return response()->json(['detail' => 'Tiket kategori tidak ditemukan'], 404);
        return response()->json($tiket);
    }

    public function store(Request $request)
    {
        try {
            $tiket = TiketKategori::create([
                'kelas_id' => $request->input('kelas_id'),
                'nama_kategori' => $request->input('nama_kategori', ''),
                'deskripsi' => $request->input('deskripsi'),
                'harga' => $request->input('harga', 0),
                'manfaat' => $request->input('manfaat'),
                'is_populer' => filter_var($request->input('is_populer', false), FILTER_VALIDATE_BOOLEAN),
                'is_active' => true,
            ]);

            return response()->json(['message' => 'Tiket kategori berhasil ditambahkan', 'tiket' => $tiket], 201);
        } catch (\Exception $e) {
            return response()->json(['detail' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $tiket = TiketKategori::find($id);
        if (!$tiket) return response()->json(['detail' => 'Tiket kategori tidak ditemukan'], 404);

        $data = $request->only(['nama_kategori', 'deskripsi', 'harga', 'manfaat', 'is_populer', 'is_active']);
        if (isset($data['is_populer'])) $data['is_populer'] = filter_var($data['is_populer'], FILTER_VALIDATE_BOOLEAN);
        if (isset($data['is_active'])) $data['is_active'] = filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN);

        $tiket->update($data);
        return response()->json(['message' => 'Tiket kategori berhasil diupdate', 'tiket' => $tiket]);
    }

    public function destroy($id)
    {
        $tiket = TiketKategori::find($id);
        if (!$tiket) return response()->json(['detail' => 'Tiket kategori tidak ditemukan'], 404);
        $tiket->delete();
        return response()->json(['message' => 'Tiket kategori berhasil dihapus']);
    }

    public function toggleActive($id)
    {
        $tiket = TiketKategori::find($id);
        if (!$tiket) return response()->json(['detail' => 'Tiket kategori tidak ditemukan'], 404);

        $tiket->update(['is_active' => !$tiket->is_active]);
        return response()->json(['message' => 'Status berhasil diubah', 'tiket' => $tiket]);
    }
}
