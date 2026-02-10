<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $data = $request->validate([
                'username' => 'required|string|unique:users,username',
                'password' => 'required|string|min:4',
                'nama_lengkap' => 'required|string',
                'email' => 'required|email',
                'no_telepon' => 'required|string',
                'alamat' => 'required|string',
            ]);

            $user = User::create([
                'username' => $data['username'],
                'password' => md5($data['password']),
                'role' => 'user',
                'nama_lengkap' => $data['nama_lengkap'],
                'email' => $data['email'],
                'no_telepon' => $data['no_telepon'],
                'alamat' => $data['alamat'],
            ]);

            $token = "1|user|{$user->username}";

            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil',
                'token' => $token,
                'access_token' => $token,
                'role' => 'user',
                'username' => $user->username,
                'nama_lengkap' => $user->nama_lengkap,
                'email' => $user->email,
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'role' => $user->role,
                    'nama_lengkap' => $user->nama_lengkap,
                    'email' => $user->email,
                    'no_telepon' => $user->no_telepon,
                    'alamat' => $user->alamat,
                ],
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['detail' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['detail' => 'Registrasi gagal: ' . $e->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        if (!$username || !$password) {
            return response()->json(['detail' => 'Username dan password wajib diisi'], 400);
        }

        $user = User::where('username', $username)->first();

        if (!$user || $user->password !== md5($password)) {
            return response()->json(['detail' => 'Username atau password salah'], 401);
        }

        $user->update(['last_login' => now()]);
        $token = "1|{$user->role}|{$user->username}";

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'role' => $user->role,
            'token' => $token,
            'access_token' => $token,
            // Flatten user fields to top level (frontend reads res.data.nama_lengkap etc.)
            'username' => $user->username,
            'nama_lengkap' => $user->nama_lengkap,
            'email' => $user->email,
            'no_telepon' => $user->no_telepon,
            'alamat' => $user->alamat,
            'foto_profil' => $user->foto_profil,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'role' => $user->role,
                'nama_lengkap' => $user->nama_lengkap,
                'email' => $user->email,
                'no_telepon' => $user->no_telepon,
                'alamat' => $user->alamat,
                'foto_profil' => $user->foto_profil,
            ],
        ]);
    }

    public function adminLogin(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        if (!$username || !$password) {
            return response()->json(['detail' => 'Username dan password wajib diisi'], 400);
        }

        $user = User::where('username', $username)->where('role', 'admin')->first();

        if (!$user || $user->password !== md5($password)) {
            return response()->json(['detail' => 'Username atau password salah'], 401);
        }

        $user->update(['last_login' => now()]);
        $token = "1|admin|{$user->username}";

        return response()->json([
            'success' => true,
            'message' => 'Login admin berhasil',
            'role' => 'admin',
            'token' => $token,
            'access_token' => $token,
            'username' => $user->username,
            'nama_lengkap' => $user->nama_lengkap,
            'email' => $user->email,
            'foto_profil' => $user->foto_profil,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'role' => $user->role,
                'nama_lengkap' => $user->nama_lengkap,
                'email' => $user->email,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        return response()->json(['message' => 'Logout berhasil']);
    }

    public function getProfile(Request $request)
    {
        $user = $request->input('auth_user');
        return response()->json([
            'id' => $user->id,
            'username' => $user->username,
            'role' => $user->role,
            'nama_lengkap' => $user->nama_lengkap,
            'email' => $user->email,
            'no_telepon' => $user->no_telepon,
            'alamat' => $user->alamat,
            'foto_profil' => $user->foto_profil,
            'last_login' => $user->last_login,
        ]);
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = User::find($request->input('auth_user_id'));
            $user->update($request->only(['nama_lengkap', 'email', 'no_telepon', 'alamat']));
            return response()->json(['message' => 'Profil berhasil diupdate', 'user' => $user]);
        } catch (\Exception $e) {
            return response()->json(['detail' => 'Gagal update profil: ' . $e->getMessage()], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        $user = User::find($request->input('auth_user_id'));
        $passwordLama = $request->input('password_lama');
        $passwordBaru = $request->input('password_baru');

        if ($user->password !== md5($passwordLama)) {
            return response()->json(['detail' => 'Password lama salah'], 400);
        }

        $user->update(['password' => md5($passwordBaru)]);
        return response()->json(['message' => 'Password berhasil diubah']);
    }

    public function uploadPhoto(Request $request)
    {
        try {
            $file = $request->file('file');
            if (!$file) {
                return response()->json(['detail' => 'File tidak ditemukan'], 400);
            }

            $user = User::find($request->input('auth_user_id'));

            // Delete old photo
            if ($user->foto_profil) {
                $oldPath = str_replace('/uploads/', '', $user->foto_profil);
                Storage::disk('public')->delete('uploads/' . $oldPath);
            }

            $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads/profile_pictures', $filename, 'public');

            $user->update(['foto_profil' => '/uploads/profile_pictures/' . $filename]);

            return response()->json([
                'foto_profil' => '/uploads/profile_pictures/' . $filename,
                'message' => 'Foto profil berhasil diupload',
            ]);
        } catch (\Exception $e) {
            return response()->json(['detail' => 'Gagal upload foto: ' . $e->getMessage()], 500);
        }
    }

    public function deletePhoto(Request $request)
    {
        $user = User::find($request->input('auth_user_id'));

        if ($user->foto_profil) {
            $path = str_replace('/uploads/', 'uploads/', $user->foto_profil);
            Storage::disk('public')->delete($path);
            $user->update(['foto_profil' => null]);
        }

        return response()->json(['message' => 'Foto profil berhasil dihapus']);
    }

    public function checkAuth(Request $request)
    {
        $user = $request->input('auth_user');
        return response()->json([
            'authenticated' => true,
            'role' => $request->input('auth_role'),
            'username' => $request->input('auth_username'),
            'user_data' => [
                'id' => $user->id,
                'username' => $user->username,
                'role' => $user->role,
                'nama_lengkap' => $user->nama_lengkap,
                'email' => $user->email,
                'no_telepon' => $user->no_telepon,
                'alamat' => $user->alamat,
                'foto_profil' => $user->foto_profil,
            ],
        ]);
    }

    public function getPublicUser($id)
    {
        $user = User::select('id', 'username', 'nama_lengkap')->find($id);
        if (!$user) {
            return response()->json(['detail' => 'User tidak ditemukan'], 404);
        }
        return response()->json($user);
    }
}
