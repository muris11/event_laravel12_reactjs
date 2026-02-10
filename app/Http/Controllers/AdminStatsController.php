<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminStatsController extends Controller
{
    public function getStats()
    {
        try {
            $stats = [
                'total_users' => DB::table('users')->count(),
                'total_items' => DB::table('items')->count(),
                'total_categories' => DB::table('categories')->count(),
                'total_units' => DB::table('item_units')->count(),
                'available_units' => DB::table('item_units')->where('status', 'Tersedia')->count(),
                'borrowed_units' => DB::table('item_units')->where('status', 'Dipinjam')->count(),
                'total_borrowings' => DB::table('borrowings')->whereNull('deleted_at')->count(),
                'pending_borrowings' => DB::table('borrowings')->where('status', 'Menunggu')->whereNull('deleted_at')->count(),
                'active_borrowings' => DB::table('borrowings')->where('status', 'Dipinjam')->whereNull('deleted_at')->count(),
                'total_kelas' => DB::table('kelas')->count(),
                'total_returns' => DB::table('returns')->count(),
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json(['detail' => 'Gagal mengambil statistik: ' . $e->getMessage()], 500);
        }
    }

    public function exportData()
    {
        try {
            $data = [
                'users' => DB::table('users')->select('id', 'username', 'role', 'nama_lengkap', 'email', 'created_at')->get(),
                'items' => DB::table('items')->get(),
                'categories' => DB::table('categories')->get(),
                'item_units' => DB::table('item_units')->get(),
                'borrowings' => DB::table('borrowings')->get(),
                'returns' => DB::table('returns')->get(),
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['detail' => 'Export gagal: ' . $e->getMessage()], 500);
        }
    }

    public function systemHealth()
    {
        try {
            DB::connection()->getPdo();
            $dbStatus = 'connected';
        } catch (\Exception $e) {
            $dbStatus = 'disconnected';
        }

        return response()->json([
            'status' => 'healthy',
            'database' => $dbStatus,
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'storage_writable' => is_writable(storage_path()),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function getLogs()
    {
        try {
            $logFile = storage_path('logs/laravel.log');
            if (!file_exists($logFile)) {
                return response()->json(['logs' => []]);
            }

            $lines = array_slice(file($logFile), -100);
            return response()->json(['logs' => $lines]);
        } catch (\Exception $e) {
            return response()->json(['detail' => 'Gagal membaca log'], 500);
        }
    }

    public function cleanup()
    {
        return response()->json(['message' => 'Cleanup selesai', 'cleaned' => 0]);
    }

    public function systemInfo()
    {
        return response()->json([
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'memory_limit' => ini_get('memory_limit'),
            'max_upload_size' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'disk_free_space' => disk_free_space('.'),
        ]);
    }
}
