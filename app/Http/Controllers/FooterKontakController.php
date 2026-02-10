<?php

namespace App\Http\Controllers;

use App\Models\FooterKontak;
use Illuminate\Http\Request;

class FooterKontakController extends Controller
{
    public function index()
    {
        $footer = FooterKontak::first();
        if (!$footer) {
            $footer = FooterKontak::create([
                'email' => 'info@gastronomi.id',
                'phone' => '(021) 1234-5678',
                'address' => 'Jakarta, Indonesia',
                'description' => 'Platform terdepan untuk manajemen inventaris dan event.',
                'copyright_text' => '© 2024 Gastronomi. All rights reserved.',
            ]);
        }
        return response()->json($footer);
    }

    public function update(Request $request)
    {
        $footer = FooterKontak::firstOrCreate([], [
            'email' => 'info@gastronomi.id',
            'phone' => '(021) 1234-5678',
        ]);

        $footer->update($request->only([
            'email', 'phone', 'address', 'description', 'copyright_text',
            'social_facebook', 'social_instagram', 'social_twitter', 'social_youtube',
        ]));

        return response()->json(['message' => 'Footer kontak berhasil diupdate', 'footer' => $footer]);
    }

    public function reset()
    {
        FooterKontak::truncate();
        $footer = FooterKontak::create([
            'email' => 'info@gastronomi.id',
            'phone' => '(021) 1234-5678',
            'address' => 'Jakarta, Indonesia',
            'description' => 'Platform terdepan untuk manajemen inventaris dan event.',
            'copyright_text' => '© 2024 Gastronomi. All rights reserved.',
        ]);
        return response()->json(['message' => 'Footer kontak berhasil direset', 'footer' => $footer]);
    }

    public function stats()
    {
        $footer = FooterKontak::first();
        $filled = 0;
        if ($footer) {
            foreach (['email', 'phone', 'address', 'description', 'social_facebook', 'social_instagram', 'social_twitter', 'social_youtube'] as $field) {
                if (!empty($footer->$field)) $filled++;
            }
        }
        return response()->json(['total_fields' => 8, 'filled_fields' => $filled]);
    }

    public function publicIndex()
    {
        $footer = FooterKontak::first();
        return response()->json($footer);
    }
}
