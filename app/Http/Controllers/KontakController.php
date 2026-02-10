<?php

namespace App\Http\Controllers;

use App\Models\Kontak;
use App\Models\ContactItem;
use App\Models\ContactDetail;
use Illuminate\Http\Request;

class KontakController extends Controller
{
    public function index()
    {
        $kontak = Kontak::first();
        $items = ContactItem::with('details')->orderBy('order_position')->get();
        return response()->json(['hero' => $kontak, 'items' => $items]);
    }

    public function updateHero(Request $request)
    {
        $kontak = Kontak::firstOrCreate([], [
            'hero_title' => 'Hubungi Kami',
            'hero_subtitle' => '',
            'hero_description' => '',
        ]);
        $kontak->update($request->only(['hero_title', 'hero_subtitle', 'hero_description']));
        return response()->json(['message' => 'Hero berhasil diupdate', 'hero' => $kontak]);
    }

    public function getItems()
    {
        $items = ContactItem::with('details')->orderBy('order_position')->get();
        return response()->json($items);
    }

    public function storeItem(Request $request)
    {
        try {
            $item = ContactItem::create([
                'icon' => $request->input('icon', 'phone'),
                'title' => $request->input('title', ''),
                'action_url' => $request->input('action_url'),
                'order_position' => $request->input('order_position', ContactItem::max('order_position') + 1),
                'is_active' => true,
            ]);

            $details = $request->input('details', []);
            foreach ($details as $i => $detail) {
                ContactDetail::create([
                    'contact_item_id' => $item->id,
                    'detail_text' => is_string($detail) ? $detail : ($detail['detail_text'] ?? ''),
                    'detail_order' => $i,
                ]);
            }

            return response()->json(['message' => 'Contact item berhasil ditambahkan', 'item' => $item->load('details')], 201);
        } catch (\Exception $e) {
            return response()->json(['detail' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    public function updateItem(Request $request, $id)
    {
        $item = ContactItem::find($id);
        if (!$item) return response()->json(['detail' => 'Item tidak ditemukan'], 404);

        $item->update($request->only(['icon', 'title', 'action_url', 'order_position', 'is_active']));

        if ($request->has('details')) {
            $item->details()->delete();
            foreach ($request->input('details', []) as $i => $detail) {
                ContactDetail::create([
                    'contact_item_id' => $item->id,
                    'detail_text' => is_string($detail) ? $detail : ($detail['detail_text'] ?? ''),
                    'detail_order' => $i,
                ]);
            }
        }

        return response()->json(['message' => 'Contact item berhasil diupdate', 'item' => $item->load('details')]);
    }

    public function destroyItem($id)
    {
        $item = ContactItem::find($id);
        if (!$item) return response()->json(['detail' => 'Item tidak ditemukan'], 404);
        $item->delete();
        return response()->json(['message' => 'Contact item berhasil dihapus']);
    }

    public function reset()
    {
        Kontak::truncate();
        ContactItem::truncate();
        ContactDetail::truncate();

        $kontak = Kontak::create([
            'hero_title' => 'Hubungi Kami',
            'hero_subtitle' => 'Kami siap membantu Anda',
            'hero_description' => 'Silakan hubungi kami melalui salah satu cara di bawah ini.',
        ]);

        return response()->json(['message' => 'Kontak berhasil direset', 'hero' => $kontak]);
    }

    public function publicIndex()
    {
        $kontak = Kontak::first();
        $items = ContactItem::with('details')
            ->where('is_active', true)
            ->orderBy('order_position')
            ->get();
        return response()->json(['hero' => $kontak, 'items' => $items]);
    }
}
