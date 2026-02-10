<?php

namespace App\Http\Controllers;

use App\Models\TentangKamiTim;
use App\Models\TentangKamiTimKeahlian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TimController extends Controller
{
    public function index()
    {
        $tim = TentangKamiTim::with('keahlian')->orderBy('urutan')->get();
        return response()->json($tim);
    }

    public function store(Request $request)
    {
        try {
            $tim = TentangKamiTim::create([
                'nama' => $request->input('nama', ''),
                'jabatan' => $request->input('jabatan', ''),
                'deskripsi' => $request->input('deskripsi'),
                'urutan' => $request->input('urutan', TentangKamiTim::max('urutan') + 1),
                'is_active' => true,
            ]);

            $keahlian = $request->input('keahlian', []);
            if (is_string($keahlian)) $keahlian = json_decode($keahlian, true) ?? [];
            foreach ($keahlian as $i => $skill) {
                TentangKamiTimKeahlian::create([
                    'tim_id' => $tim->id,
                    'keahlian' => is_string($skill) ? $skill : ($skill['keahlian'] ?? ''),
                    'urutan' => $i,
                ]);
            }

            return response()->json(['message' => 'Anggota tim berhasil ditambahkan', 'tim' => $tim->load('keahlian')], 201);
        } catch (\Exception $e) {
            return response()->json(['detail' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $tim = TentangKamiTim::find($id);
        if (!$tim) return response()->json(['detail' => 'Tim tidak ditemukan'], 404);

        $tim->update($request->only(['nama', 'jabatan', 'deskripsi', 'urutan', 'is_active']));

        if ($request->has('keahlian')) {
            $tim->keahlian()->delete();
            $keahlian = $request->input('keahlian', []);
            if (is_string($keahlian)) $keahlian = json_decode($keahlian, true) ?? [];
            foreach ($keahlian as $i => $skill) {
                TentangKamiTimKeahlian::create([
                    'tim_id' => $tim->id,
                    'keahlian' => is_string($skill) ? $skill : ($skill['keahlian'] ?? ''),
                    'urutan' => $i,
                ]);
            }
        }

        return response()->json(['message' => 'Tim berhasil diupdate', 'tim' => $tim->load('keahlian')]);
    }

    public function destroy($id)
    {
        $tim = TentangKamiTim::find($id);
        if (!$tim) return response()->json(['detail' => 'Tim tidak ditemukan'], 404);

        if ($tim->foto) {
            Storage::disk('public')->delete(ltrim($tim->foto, '/'));
        }
        $tim->delete();
        return response()->json(['message' => 'Anggota tim berhasil dihapus']);
    }

    public function uploadFoto(Request $request, $id)
    {
        try {
            $tim = TentangKamiTim::find($id);
            if (!$tim) return response()->json(['detail' => 'Tim tidak ditemukan'], 404);

            $file = $request->file('file') ?? $request->file('foto');
            if (!$file) return response()->json(['detail' => 'File tidak ditemukan'], 400);

            if ($tim->foto) {
                Storage::disk('public')->delete(ltrim($tim->foto, '/'));
            }

            $filename = 'tim_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('uploads/tim', $filename, 'public');
            $tim->update(['foto' => '/uploads/tim/' . $filename]);

            return response()->json(['foto' => '/uploads/tim/' . $filename, 'message' => 'Foto berhasil diupload']);
        } catch (\Exception $e) {
            return response()->json(['detail' => 'Gagal upload foto: ' . $e->getMessage()], 500);
        }
    }

    public function deleteFoto($id)
    {
        $tim = TentangKamiTim::find($id);
        if (!$tim) return response()->json(['detail' => 'Tim tidak ditemukan'], 404);

        if ($tim->foto) {
            Storage::disk('public')->delete(ltrim($tim->foto, '/'));
            $tim->update(['foto' => null]);
        }

        return response()->json(['message' => 'Foto berhasil dihapus']);
    }

    public function publicIndex()
    {
        $tim = TentangKamiTim::with('keahlian')
            ->where('is_active', true)
            ->orderBy('urutan')
            ->get();
        return response()->json($tim);
    }
}
