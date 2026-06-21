# Blueprint Sistem Dashboard Re-upload Facebook Reels, YouTube Shorts, dan TikTok

## Spesifikasi Teknis Inti
* **Backend Framework**: Laravel 11 *(Catatan: disesuaikan dengan versi rilis stabil saat ini)*
* **PHP Version**: 8.4
* **Database**: MySQL
* **Admin Dashboard**: Laravel Filament (Multi-panel: Super Admin Panel dan Client Area)
* **Frontend Component**: Livewire
* **Landing Page dan Website**: Laravel Blade
* **Payment Gateway**: Midtrans

## Fitur-Fitur Aplikasi yang Tersedia

### Fitur Landing Page dan Website Depan
* **Home Page**: Menampilkan USP produk, fitur unggulan, testimoni, dan CTA untuk registrasi.
* **Pricing Page**: Menampilkan paket langganan secara jelas dengan detail harga dan perbandingan fitur antarpaket.
* **Halaman Tambahan**: Halaman Syarat dan Ketentuan, Kebijakan Privasi, FAQ, serta Blog atau Dokumentasi untuk edukasi pengguna dan optimasi SEO.

### Fitur Autentikasi dan Akun
* **Registrasi Pengguna**: Pendaftaran dengan email dan password, dilengkapi sistem verifikasi email untuk keamanan.
* **Social Login**: Opsi pendaftaran dan login cepat menggunakan akun Google dan Facebook.
* **Platform Integrations**: Pengguna dapat menghubungkan (Oauth/Token) akun Facebook, Channel YouTube, dan akun TikTok mereka untuk kebutuhan pengunggahan otomatis (*auto-publish*).
* **Manajemen Profil**: Pengguna dapat memperbarui data pribadi, kata sandi, dan informasi perbankan untuk pencairan komisi afiliasi.

### Fitur Utama Sistem Aplikasi
* **Facebook Login Integrator**: Fitur untuk menghubungkan akun Facebook dan meminta Page Access Token.
* **Scraper & Lister Reels**: Sistem untuk memindai Reels Facebook dari keyword tertarget dan menyimpannya sebagai tautan sumber di database.
* **AI Content Remaker**: Mesin pemroses teks untuk meremake deskripsi video menjadi gaya bahasa clickbait yang menarik. Pengguna dapat memilih penyedia AI (Naim Router atau Google Gemini API).
* **Multi-Platform Scheduled Post Manager**: Sistem penjadwalan otomatis untuk mengunggah konten yang sudah diproses pada waktu spesifik secara silang ke berbagai platform (Facebook Reels, YouTube Shorts, dan TikTok).
* **Multi-Account Dashboard**: Admin dapat melihat statistik keseluruhan penggunaan, sedangkan panel pengguna terbatas pada aktivitas video mereka sendiri.

### Fitur Langganan dan Keuangan
* **Upgrade/Downgrade Paket**: Pengguna dapat memilih untuk memperbarui paket langganan mereka secara berkala.
* **Integrasi Midtrans**: Sistem pembayaran otomatis dengan Midtrans untuk berbagai metode seperti QRIS, Gopay, Virtual Account, hingga Bank Transfer. Semua paket berbasis langganan bulanan atau tahunan.

### Fitur Afiliasi
* **Affiliate Dashboard**: Melacak jumlah klik tautan referral, pendaftar baru dari tautan tersebut, dan komisi yang didapat.
* **Masa Aktif Cookie**: Sistem pelacakan afiliasi yang aktif hingga 30 hari sejak tautan diklik.

## Struktur Database dan Relasi Tabel

### Tabel Users
Menyimpan data pengguna, hak akses langganan, dan token platform (Facebook, YouTube, TikTok).
* `id` (Primary Key)
* `name`
* `email`
* `email_verified_at`
* `password`
* `role` (super_admin, user)
* `subscription_plan` (starter, pro, agency)
* `bank_account_number`
* `can_post_youtube` (boolean)
* `can_post_tiktok` (boolean)
* `youtube_access_token`
* `youtube_refresh_token`
* `tiktok_access_token`
* `created_at`, `updated_at`

### Tabel Videos
Menyimpan tautan video asli hasil scraping dari Facebook.
* `id` (Primary Key)
* `user_id` (Foreign Key ke users.id)
* `facebook_video_id`
* `source_url`
* `created_at`, `updated_at`

### Tabel Ai Contents
Menyimpan teks deskripsi baru yang dihasilkan oleh AI.
* `id` (Primary Key)
* `video_id` (Foreign Key ke videos.id)
* `original_text`
* `ai_remake_text`
* `ai_provider`
* `created_at`, `updated_at`

### Tabel Scheduled Posts
Mencatat data postingan yang dijadwal ke Facebook, YouTube, maupun TikTok.
* `id` (Primary Key)
* `ai_content_id` (Foreign Key ke ai_contents.id)
* `platform` (enum: facebook, youtube, tiktok)
* `scheduled_time`
* `status` (pending, scheduled, published, failed)
* `platform_post_id` (Menyimpan ID Video sukses di platform tujuan)
* `error_message` (Mencatat log jika gagal)
* `created_at`, `updated_at`

### Tabel Transactions
Mencatat riwayat pembayaran langganan melalui Midtrans.
* `id` (Primary Key)
* `user_id` (Foreign Key ke users.id)
* `order_id`
* `midtrans_transaction_id`
* `amount`
* `plan_type`
* `payment_status`
* `payment_type`
* `snap_token`
* `created_at`, `updated_at`

### Tabel Affiliates
Menghubungkan catatan afiliasi dan komisi yang diperoleh pengguna.
* `id` (Primary Key)
* `user_id` (Foreign Key ke users.id - pemilik tautan afiliasi)
* `referred_user_id` (Foreign Key ke users.id - pengguna yang mendaftar)
* `cookie_expiry_date`
* `total_clicks`
* `total_commission`
* `payout_status`
* `created_at`, `updated_at`
