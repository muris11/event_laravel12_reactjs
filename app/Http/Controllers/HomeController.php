<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function getRiwayat(Request $request)
    {
        try {
            $userId = $request->input('auth_user_id');

            $borrowings = DB::table('borrowings')
                ->leftJoin('items', 'borrowings.barang_id', '=', 'items.id')
                ->leftJoin('returns', 'borrowings.id', '=', 'returns.borrowing_id')
                ->where('borrowings.user_id', $userId)
                ->whereNull('borrowings.deleted_at')
                ->select(
                    'borrowings.*',
                    'items.nama_barang',
                    'items.foto as foto_barang',
                    'returns.tanggal_pengembalian',
                    'returns.kondisi_barang as kondisi_pengembalian',
                    'returns.catatan as catatan_pengembalian'
                )
                ->orderBy('borrowings.created_at', 'desc')
                ->get();

            $result = $borrowings->map(function ($b) {
                $foto = $b->foto_barang;
                if ($foto && !str_starts_with($foto, 'http') && !str_starts_with($foto, '/')) {
                    $foto = '/uploads/' . $foto;
                }

                return [
                    'id' => $b->id,
                    'nama_peminjam' => $b->nama_peminjam,
                    'barang_id' => $b->barang_id,
                    'nama_barang' => $b->nama_barang,
                    'unit_kode' => $b->unit_kode,
                    'tanggal_pinjam' => $b->tanggal_pinjam,
                    'tanggal_kembali' => $b->tanggal_kembali,
                    'keperluan' => $b->keperluan,
                    'status' => $b->status,
                    'alasan_penolakan' => $b->alasan_penolakan,
                    'foto_barang' => $foto,
                    'tanggal_pengembalian' => $b->tanggal_pengembalian,
                    'kondisi_pengembalian' => $b->kondisi_pengembalian,
                    'catatan_pengembalian' => $b->catatan_pengembalian,
                    'created_at' => $b->created_at,
                ];
            });

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['detail' => 'Gagal mengambil riwayat: ' . $e->getMessage()], 500);
        }
    }

    public function getPengembalian($id)
    {
        $ret = DB::table('returns')->where('borrowing_id', $id)->first();
        if (!$ret) return response()->json(['detail' => 'Data pengembalian tidak ditemukan'], 404);
        return response()->json($ret);
    }
}
