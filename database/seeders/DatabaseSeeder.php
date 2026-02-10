<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\FooterKontak;
use App\Models\Kontak;
use App\Models\SliderEvent;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ============ USERS ============
        User::create([
            'username' => 'admin',
            'password' => md5('admin123'),
            'role' => 'admin',
            'nama_lengkap' => 'Administrator',
            'email' => 'admin@gastronomi.id',
        ]);

        User::create([
            'username' => 'user',
            'password' => md5('user123'),
            'role' => 'user',
            'nama_lengkap' => 'Budi Santoso',
            'email' => 'budi@gastronomi.id',
            'no_telepon' => '081234567890',
            'alamat' => 'Jl. Sudirman No. 45, Jakarta Selatan',
        ]);

        // ============ CATEGORIES ============
        $categories = ['Workshop', 'Seminar', 'Kursus', 'Festival', 'Kompetisi'];
        foreach ($categories as $cat) {
            Category::create(['nama' => $cat]);
        }

        // ============ KELAS (EVENTS) ============
        $kelasData = [
            [
                'nama_kelas' => 'Workshop Memasak Italia: Pasta & Risotto',
                'kategori_id' => 1,
                'deskripsi' => 'Pelajari teknik memasak pasta dan risotto autentik dari chef profesional. Workshop ini mencakup pembuatan pasta dari awal, saus klasik Italia, dan teknik memasak risotto yang sempurna.',
                'jadwal' => '2026-03-15 09:00 - 15:00 WIB',
                'ruangan' => 'Kitchen Studio A - Gedung Gastronomi Lt. 2',
                'biaya' => 750000,
                'total_peserta' => 25,
                'gambaran_event' => '<h3>Apa yang akan dipelajari?</h3><ul><li>Teknik membuat pasta fresh dari awal</li><li>5 jenis saus klasik Italia</li><li>Risotto alla Milanese</li><li>Plating ala restoran bintang 5</li></ul><h3>Fasilitas</h3><ul><li>Bahan masak premium</li><li>Celemek & peralatan</li><li>Sertifikat keikutsertaan</li><li>Makan siang bersama</li></ul>',
                'link_navigasi' => 'https://maps.google.com/?q=Jakarta+Kitchen+Studio',
                'is_link_eksternal' => true,
            ],
            [
                'nama_kelas' => 'Seminar Gastronomi Nusantara: Rempah & Cita Rasa',
                'kategori_id' => 2,
                'deskripsi' => 'Eksplorasi kekayaan rempah Indonesia dan bagaimana rempah membentuk identitas kuliner Nusantara. Dipandu oleh pakar kuliner dan food historian terkemuka.',
                'jadwal' => '2026-04-10 13:00 - 17:00 WIB',
                'ruangan' => 'Auditorium Utama - Gedung Gastronomi Lt. 5',
                'biaya' => 350000,
                'total_peserta' => 100,
                'gambaran_event' => '<h3>Topik Seminar</h3><ul><li>Sejarah rempah Indonesia di jalur perdagangan dunia</li><li>Pemetaan rempah dari Sabang sampai Merauke</li><li>Teknik penggunaan rempah dalam masakan modern</li><li>Food pairing: seni mencocokkan rempah</li></ul><h3>Pembicara</h3><ul><li>Dr. Rina Kusuma - Food Historian</li><li>Chef Juna - Celebrity Chef</li></ul>',
                'link_navigasi' => '',
                'is_link_eksternal' => false,
            ],
            [
                'nama_kelas' => 'Festival Kopi Indonesia 2026',
                'kategori_id' => 4,
                'deskripsi' => 'Festival terbesar yang mempertemukan petani kopi, roaster, barista, dan pecinta kopi dari seluruh Indonesia. Nikmati cupping session, latte art competition, dan pameran kopi.',
                'jadwal' => '2026-05-20 - 2026-05-22 (3 hari)',
                'ruangan' => 'Convention Hall - Gedung Gastronomi',
                'biaya' => 150000,
                'total_peserta' => 500,
                'gambaran_event' => '<h3>Highlight Festival</h3><ul><li>Cupping session 50+ varietas kopi Indonesia</li><li>Latte Art Competition tingkat nasional</li><li>Brewing workshop oleh barista champion</li><li>Pameran alat kopi & merchandise</li><li>Live music & food court</li></ul><h3>Special Guest</h3><p>Mikael Jasin - Indonesia Barista Champion 2024</p>',
                'link_navigasi' => 'https://maps.google.com/?q=Jakarta+Convention+Hall',
                'is_link_eksternal' => true,
            ],
        ];

        foreach ($kelasData as $kelas) {
            DB::table('kelas')->insert(array_merge($kelas, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ============ TIKET KATEGORI ============
        $tiketData = [
            // Kelas 1 - Workshop Pasta
            ['kelas_id' => 1, 'nama_kategori' => 'Regular', 'deskripsi' => 'Akses workshop standar dengan semua fasilitas dasar', 'harga' => 750000, 'manfaat' => 'Bahan masak,Celemek,Resep booklet,Sertifikat', 'is_populer' => false, 'is_active' => true],
            ['kelas_id' => 1, 'nama_kategori' => 'VIP', 'deskripsi' => 'Akses premium dengan private session bersama chef', 'harga' => 1500000, 'manfaat' => 'Semua fasilitas Regular,Private session 30 menit,Cookbook eksklusif,Hampers premium,Foto bersama chef', 'is_populer' => true, 'is_active' => true],
            // Kelas 2 - Seminar Rempah
            ['kelas_id' => 2, 'nama_kategori' => 'Early Bird', 'deskripsi' => 'Harga spesial untuk pendaftar awal', 'harga' => 250000, 'manfaat' => 'Akses seminar,Materi digital,Coffee break,Sertifikat', 'is_populer' => true, 'is_active' => true],
            ['kelas_id' => 2, 'nama_kategori' => 'Regular', 'deskripsi' => 'Tiket reguler seminar', 'harga' => 350000, 'manfaat' => 'Akses seminar,Materi digital,Coffee break,Sertifikat,Goodie bag', 'is_populer' => false, 'is_active' => true],
            // Kelas 3 - Festival Kopi
            ['kelas_id' => 3, 'nama_kategori' => 'Day Pass', 'deskripsi' => 'Akses 1 hari festival', 'harga' => 150000, 'manfaat' => 'Akses pameran,Free cupping 3x,Tote bag festival', 'is_populer' => false, 'is_active' => true],
            ['kelas_id' => 3, 'nama_kategori' => 'Full Pass (3 Hari)', 'deskripsi' => 'Akses penuh 3 hari semua area dan workshop', 'harga' => 350000, 'manfaat' => 'Akses 3 hari penuh,Free cupping unlimited,Semua workshop,Merchandise eksklusif,Meet & greet barista', 'is_populer' => true, 'is_active' => true],
        ];

        foreach ($tiketData as $tiket) {
            DB::table('tiket_kategori')->insert(array_merge($tiket, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ============ TENTANG KAMI ============
        $tentangKamiData = [
            ['section' => 'hero', 'section_key' => 'title', 'content_type' => 'text', 'content_value' => 'TENTANG KAMI'],
            ['section' => 'hero', 'section_key' => 'subtitle', 'content_type' => 'text', 'content_value' => 'Menginspirasi Melalui Gastronomi'],
            ['section' => 'hero', 'section_key' => 'description', 'content_type' => 'text', 'content_value' => 'Gastronomi Run adalah platform terdepan yang menghubungkan pecinta kuliner dengan pengalaman gastronomi terbaik di Indonesia. Kami percaya bahwa makanan bukan sekadar kebutuhan, melainkan seni dan budaya yang perlu dilestarikan.'],
            ['section' => 'story', 'section_key' => 'title', 'content_type' => 'text', 'content_value' => 'Cerita Kami'],
            ['section' => 'story', 'section_key' => 'content', 'content_type' => 'text', 'content_value' => 'Berawal dari kecintaan terhadap kuliner Indonesia, Gastronomi Run didirikan pada tahun 2023 dengan visi menjadi jembatan antara tradisi kuliner Nusantara dan inovasi gastronomi modern. Kami menyelenggarakan berbagai workshop, seminar, dan festival yang mengangkat kekayaan rasa dan budaya makan Indonesia.'],
            ['section' => 'vision', 'section_key' => 'title', 'content_type' => 'text', 'content_value' => 'Visi Kami'],
            ['section' => 'vision', 'section_key' => 'content', 'content_type' => 'text', 'content_value' => 'Menjadi platform gastronomi nomor satu di Asia Tenggara yang menginspirasi, mengedukasi, dan menghubungkan komunitas kuliner.'],
            ['section' => 'mission', 'section_key' => 'title', 'content_type' => 'text', 'content_value' => 'Misi Kami'],
            ['section' => 'mission', 'section_key' => 'content', 'content_type' => 'text', 'content_value' => 'Menyelenggarakan event kuliner berkualitas tinggi. Mendukung chef lokal dan UMKM kuliner. Melestarikan tradisi kuliner Nusantara melalui edukasi. Membangun jaringan komunitas gastronomi nasional.'],
        ];

        foreach ($tentangKamiData as $tk) {
            DB::table('tentang_kami')->insert(array_merge($tk, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ============ TENTANG KAMI TIM ============
        $timData = [
            [
                'nama' => 'Chef Andi Pratama',
                'jabatan' => 'Founder & Head Chef',
                'deskripsi' => 'Chef berpengalaman 15 tahun di restoran bintang Michelin. Lulusan Le Cordon Bleu Paris dengan spesialisasi French-Indonesian fusion cuisine.',
                'urutan' => 1,
                'is_active' => true,
            ],
            [
                'nama' => 'Sari Wulandari',
                'jabatan' => 'Creative Director',
                'deskripsi' => 'Ahli branding kuliner dengan pengalaman di industri F&B selama 10 tahun. Bertanggung jawab atas desain pengalaman event dan konten kreatif.',
                'urutan' => 2,
                'is_active' => true,
            ],
            [
                'nama' => 'Reza Mahendra',
                'jabatan' => 'Event Manager',
                'deskripsi' => 'Profesional event management yang telah mengelola lebih dari 200 event kuliner. Spesialisasi dalam festival skala besar dan workshop intimate.',
                'urutan' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($timData as $tim) {
            DB::table('tentang_kami_tim')->insert(array_merge($tim, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Keahlian Tim
        $keahlianData = [
            ['tim_id' => 1, 'keahlian' => 'French Cuisine', 'urutan' => 1],
            ['tim_id' => 1, 'keahlian' => 'Indonesian Fusion', 'urutan' => 2],
            ['tim_id' => 1, 'keahlian' => 'Pastry Arts', 'urutan' => 3],
            ['tim_id' => 2, 'keahlian' => 'Brand Strategy', 'urutan' => 1],
            ['tim_id' => 2, 'keahlian' => 'Content Creation', 'urutan' => 2],
            ['tim_id' => 2, 'keahlian' => 'UI/UX Design', 'urutan' => 3],
            ['tim_id' => 3, 'keahlian' => 'Event Planning', 'urutan' => 1],
            ['tim_id' => 3, 'keahlian' => 'Logistics', 'urutan' => 2],
            ['tim_id' => 3, 'keahlian' => 'Sponsorship', 'urutan' => 3],
        ];

        foreach ($keahlianData as $kh) {
            DB::table('tentang_kami_tim_keahlian')->insert(array_merge($kh, [
                'created_at' => now(),
            ]));
        }

        // ============ PARTNER ============
        $partnerData = [
            ['section' => 'hero', 'section_key' => 'title', 'content_type' => 'text', 'content_value' => 'PARTNER KAMI'],
            ['section' => 'hero', 'section_key' => 'subtitle', 'content_type' => 'text', 'content_value' => 'Bersama Membangun Ekosistem Gastronomi'],
            ['section' => 'hero', 'section_key' => 'description', 'content_type' => 'text', 'content_value' => 'Kami berkolaborasi dengan berbagai brand dan institusi untuk menghadirkan pengalaman gastronomi terbaik bagi komunitas.'],
            ['section' => 'partners', 'section_key' => 'list', 'content_type' => 'json', 'content_value' => json_encode([
                ['name' => 'Kopi Kenangan', 'description' => 'Partner resmi penyedia kopi untuk seluruh event', 'category' => 'sponsor', 'logo' => '', 'website' => 'https://kopikenangan.com', 'order' => 1],
                ['name' => 'Tokopedia', 'description' => 'Platform e-commerce untuk tiket dan merchandise', 'category' => 'sponsor', 'logo' => '', 'website' => 'https://tokopedia.com', 'order' => 2],
                ['name' => 'Le Cordon Bleu', 'description' => 'Mitra edukasi dan sertifikasi kuliner internasional', 'category' => 'education', 'logo' => '', 'website' => 'https://cordonbleu.edu', 'order' => 3],
                ['name' => 'Kompas Media', 'description' => 'Media partner untuk publikasi event nasional', 'category' => 'media', 'logo' => '', 'website' => 'https://kompas.com', 'order' => 4],
            ])],
        ];

        foreach ($partnerData as $p) {
            DB::table('partner')->insert(array_merge($p, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ============ LAYANAN ============
        $layananData = [
            ['section' => 'hero', 'section_key' => 'title', 'content_type' => 'text', 'content_value' => 'LAYANAN KAMI'],
            ['section' => 'hero', 'section_key' => 'subtitle', 'content_type' => 'text', 'content_value' => 'Solusi Gastronomi Terlengkap'],
            ['section' => 'hero', 'section_key' => 'description', 'content_type' => 'text', 'content_value' => 'Kami menyediakan berbagai layanan untuk mendukung perjalanan kuliner Anda, dari workshop hingga konsultasi bisnis F&B.'],
            ['section' => 'services', 'section_key' => 'list', 'content_type' => 'json', 'content_value' => json_encode([
                ['title' => 'Workshop & Kursus Memasak', 'description' => 'Pelajari berbagai teknik memasak dari chef profesional dalam sesi hands-on yang interaktif.', 'icon' => 'utensils', 'order' => 1],
                ['title' => 'Seminar & Talkshow Kuliner', 'description' => 'Wawasan mendalam tentang tren gastronomi, food science, dan bisnis kuliner dari para ahli.', 'icon' => 'mic', 'order' => 2],
                ['title' => 'Festival & Event Kuliner', 'description' => 'Pengalaman kuliner skala besar dengan pameran, kompetisi, dan pertunjukan memasak live.', 'icon' => 'calendar', 'order' => 3],
                ['title' => 'Konsultasi Bisnis F&B', 'description' => 'Bimbingan profesional untuk memulai dan mengembangkan bisnis kuliner Anda.', 'icon' => 'briefcase', 'order' => 4],
                ['title' => 'Catering Premium', 'description' => 'Layanan catering berkelas untuk acara korporat, pernikahan, dan event spesial.', 'icon' => 'truck', 'order' => 5],
                ['title' => 'Private Dining Experience', 'description' => 'Pengalaman makan malam eksklusif dengan menu custom dari chef bintang Michelin.', 'icon' => 'star', 'order' => 6],
            ])],
            ['section' => 'kontak_info', 'section_key' => 'phone', 'content_type' => 'text', 'content_value' => '(021) 1234-5678'],
            ['section' => 'kontak_info', 'section_key' => 'email', 'content_type' => 'text', 'content_value' => 'layanan@gastronomi.id'],
        ];

        foreach ($layananData as $l) {
            DB::table('layanan')->insert(array_merge($l, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ============ KONTAK ============
        Kontak::create([
            'hero_title' => 'Hubungi Kami',
            'hero_subtitle' => 'Kami Siap Membantu Anda',
            'hero_description' => 'Punya pertanyaan tentang event, layanan, atau kerjasama? Tim kami siap membantu Anda. Hubungi kami melalui salah satu cara di bawah ini.',
        ]);

        // ============ CONTACT ITEMS ============
        $contactItems = [
            ['icon' => 'phone', 'title' => 'Telepon', 'action_url' => 'tel:+62211234567', 'order_position' => 1, 'is_active' => true],
            ['icon' => 'mail', 'title' => 'Email', 'action_url' => 'mailto:info@gastronomi.id', 'order_position' => 2, 'is_active' => true],
            ['icon' => 'message-circle', 'title' => 'WhatsApp', 'action_url' => 'https://wa.me/6281234567890', 'order_position' => 3, 'is_active' => true],
            ['icon' => 'map-pin', 'title' => 'Alamat', 'action_url' => 'https://maps.google.com/?q=Jakarta', 'order_position' => 4, 'is_active' => true],
        ];

        foreach ($contactItems as $ci) {
            DB::table('contact_items')->insert(array_merge($ci, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Contact details
        $contactDetails = [
            ['contact_item_id' => 1, 'detail_text' => '(021) 1234-5678', 'detail_order' => 1],
            ['contact_item_id' => 1, 'detail_text' => '(021) 8765-4321', 'detail_order' => 2],
            ['contact_item_id' => 2, 'detail_text' => 'info@gastronomi.id', 'detail_order' => 1],
            ['contact_item_id' => 2, 'detail_text' => 'event@gastronomi.id', 'detail_order' => 2],
            ['contact_item_id' => 3, 'detail_text' => '+62 812-3456-7890', 'detail_order' => 1],
            ['contact_item_id' => 4, 'detail_text' => 'Jl. Gastronomi Raya No. 42, Senayan, Jakarta Selatan 12190', 'detail_order' => 1],
        ];

        foreach ($contactDetails as $cd) {
            DB::table('contact_details')->insert(array_merge($cd, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ============ FOOTER KONTAK ============
        FooterKontak::create([
            'email' => 'info@gastronomi.id',
            'phone' => '(021) 1234-5678',
            'address' => 'Jl. Gastronomi Raya No. 42, Senayan, Jakarta Selatan 12190',
            'description' => 'Platform terdepan untuk pengalaman gastronomi dan event kuliner terbaik di Indonesia.',
            'copyright_text' => '© 2024 Gastronomi Run. All rights reserved.',
            'social_facebook' => 'https://facebook.com/gastronomirun',
            'social_instagram' => 'https://instagram.com/gastronomirun',
            'social_twitter' => 'https://twitter.com/gastronomirun',
            'social_youtube' => 'https://youtube.com/@gastronomirun',
        ]);

        // ============ SLIDER EVENTS ============
        SliderEvent::create(['selected_events' => [1, 2, 3]]);
    }
}
