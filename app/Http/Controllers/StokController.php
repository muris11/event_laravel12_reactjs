<?php

namespace App\Http\Controllers;

use App\Models\ItemUnit;
use Illuminate\Http\Request;

class StokController extends Controller
{
    public function index($barangId)
    {
        $units = ItemUnit::where('barang_id', $barangId)->orderBy('kode')->get();
        return response()->json($units);
    }

    public function show($barangId, $kode)
    {
        $unit = ItemUnit::where('barang_id', $barangId)->where('kode', $kode)->first();
        if (!$unit) return response()->json(['detail' => 'Unit tidak ditemukan'], 404);
        return response()->json($unit);
    }

    public function update(Request $request, $barangId, $kode)
    {
        $unit = ItemUnit::where('barang_id', $barangId)->where('kode', $kode)->first();
        if (!$unit) return response()->json(['detail' => 'Unit tidak ditemukan'], 404);

        $data = $request->only(['kondisi', 'status']);
        $unit->update($data);

        return response()->json(['message' => 'Unit berhasil diupdate', 'unit' => $unit->fresh()]);
    }

    public function destroy($barangId, $kode)
    {
        $unit = ItemUnit::where('barang_id', $barangId)->where('kode', $kode)->first();
        if (!$unit) return response()->json(['detail' => 'Unit tidak ditemukan'], 404);

        $unit->delete();
        return response()->json(['message' => 'Unit berhasil dihapus']);
    }

    public function bulkDelete(Request $request, $barangId)
    {
        $kodes = $request->input('kodes', []);
        if (empty($kodes)) {
            return response()->json(['detail' => 'Tidak ada kode unit yang diberikan'], 400);
        }

        $deleted = ItemUnit::where('barang_id', $barangId)->whereIn('kode', $kodes)->delete();
        return response()->json(['message' => "{$deleted} unit berhasil dihapus"]);
    }
}
