# PasarKita — Marketplace Tradisional

Aplikasi demonstrasi marketplace menggunakan PHP Native, MySQLi, MySQL, Bootstrap 5, dan session PHP. Dirancang agar dapat dipasang di shared hosting seperti InfinityFree.

## Fitur
- Katalog dan pencarian produk
- Detail produk
- Registrasi dan login pelanggan
- Keranjang berbasis session
- Checkout dengan validasi stok dan transaksi database
- Admin: CRUD produk (hapus = nonaktifkan)
- Database relasional dengan 7 tabel

## Instalasi di InfinityFree
1. Buat akun/website di InfinityFree.
2. Dari Control Panel, buka **MySQL Databases**, buat database dan catat hostname, username, nama database, serta password.
3. Buka `config/database.php`, ganti `sqlXXX.infinityfree.com`, `if0_XXXXXXXX`, `PASSWORD_DATABASE`, dan nama database sesuai data dari panel.
4. Buka **Online File Manager** atau FTP, masuk ke `htdocs`, lalu unggah seluruh isi folder proyek (bukan folder pembungkusnya).
5. Buka **phpMyAdmin** dari panel InfinityFree. Pilih database yang dibuat, tab Import, lalu impor `database.sql`.
   - Pilih database hosting yang dibuat pada langkah 2 sebelum melakukan Import. File SQL sudah disiapkan tanpa perintah CREATE DATABASE agar cocok dengan hak akses shared hosting.
6. Buka domain website. Halaman katalog akan tampil.

## Login admin awal
- Email: `admin@pasarkita.test`
- Password: `admin123`

Segera ganti password admin setelah instalasi. Untuk penggunaan publik, buat halaman penggantian password atau ubah hash password admin langsung di database menggunakan hash dari `password_hash()` PHP.

## Catatan penting
- Keranjang disimpan dalam session browser; tabel `carts` dan `cart_items` disediakan sebagai rancangan relasional/pengembangan berikutnya.
- Gambar produk contoh memakai URL gambar eksternal. Admin dapat mengganti URL gambar melalui form produk.
- Hapus produk dilakukan dengan menonaktifkan produk agar riwayat transaksi tetap aman.
- Gunakan HTTPS pada domain aktif.
- Ini proyek pembelajaran. Sebelum penggunaan produksi, tambahkan CSRF token, pembatasan percobaan login, validasi URL gambar yang lebih ketat, dan halaman pengelolaan status pesanan.

## Analisis dan perancangan
Dokumen ERD, flowchart transaksi, daftar entitas, dan mockup UI tersedia di `docs/perancangan.md`.

### Entitas
1. Users: akun admin dan pembeli.
2. Categories: kategori produk.
3. Products: barang yang dijual.
4. Carts: keranjang per pengguna (struktur untuk persistensi).
5. CartItems: baris produk pada keranjang.
6. Orders: transaksi pemesanan.
7. OrderItems: rincian produk dalam pesanan.

### Relasi
- Categories 1 — N Products.
- Users 1 — 1 Carts.
- Carts 1 — N CartItems.
- Products 1 — N CartItems.
- Users 1 — N Orders.
- Orders 1 — N OrderItems.
- Products 1 — N OrderItems (product_id dapat NULL untuk menjaga snapshot jika produk dihapus).

### Alur transaksi
1. Pembeli membuka katalog dan memilih produk.
2. Pembeli melihat detail, menentukan jumlah, lalu menambahkan produk ke keranjang.
3. Sistem menyimpan item pada session dan memvalidasi ketersediaan stok.
4. Pembeli login/registrasi lalu mengisi penerima, telepon, dan alamat.
5. Sistem memulai transaksi database, mengunci/memeriksa stok, menyimpan Orders dan OrderItems, lalu mengurangi stok.
6. Jika semua berhasil, transaksi di-commit dan keranjang dikosongkan. Jika gagal, transaksi di-rollback.

### Rancangan UI
- Daftar produk: navbar hijau, hero singkat, pencarian, grid kartu produk berisi foto, nama, harga, dan tombol detail.
- Detail produk: foto besar di kiri, kategori/nama/harga/deskripsi/stok serta kontrol jumlah dan tombol tambah di kanan.
- Keranjang: tabel produk, harga, input jumlah, subtotal, total, dan tombol checkout.
