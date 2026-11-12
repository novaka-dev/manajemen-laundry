# Spesifikasi Sistem: Sistem Informasi Manajemen Operasional dan Transaksi Laundry Berbasis Web

> Dokumen ini merangkum semua keputusan desain proyek (tugas kuliah) yang sudah disepakati.
> Gunakan sebagai sumber kebenaran. Jangan menambah fitur di luar dokumen ini tanpa konfirmasi.
> Poin bertanda **[ASUMSI]** belum dikonfirmasi eksplisit. Poin di bagian "Hal yang Masih Terbuka" belum diputuskan.

---

## 1. Ringkasan

Aplikasi web untuk mengelola operasional dan transaksi satu usaha laundry yang punya outlet fisik.

- Pelanggan **datang langsung ke outlet**. Tidak ada pemesanan online dan pelanggan **tidak punya akun**.
- Seluruh data diinput oleh pegawai/kasir.
- Pembayaran dilakukan **di outlet**, lewat **tunai** atau **QRIS statis**, dan dicatat oleh kasir.
- Fokus sistem: penerimaan cucian, perhitungan biaya, pencatatan pembayaran, pelacakan status cucian, dan laporan pendapatan.

## 2. Teknologi dan Arsitektur

- **Framework:** Laravel (PHP)
- **Database:** MySQL
- **Web server:** Nginx / Apache + PHP
- **Pola:** client-server. Pengguna mengakses lewat browser.

Aplikasi Laravel memakai **3 layer**:

| Layer | Isi |
|---|---|
| 1. Presentation layer (panel web) | Menu bersama (login, dashboard), menu admin, menu pegawai |
| 2. Application & business logic layer | Routes, Controller, Auth & role access (middleware), dan 7 service (lihat di bawah) |
| 3. Data layer | Eloquent models + database MySQL (8 tabel) |

### Service di business logic layer

| Service | Tanggung jawab |
|---|---|
| User | Kelola akun admin dan pegawai |
| Tariff | Kelola jenis layanan dan tarif |
| Customer | CRUD data pelanggan |
| Transaction | Buat transaksi, hitung total (jumlah × tarif), buat nomor transaksi, hasilkan nota |
| Payment | Catat pembayaran (tunai/QRIS), perbarui status bayar |
| Laundry status | Perbarui status pengerjaan cucian |
| Report | Rekap transaksi dan pendapatan |

### Output sistem

- Nota transaksi (dari Transaction)
- Status cucian (dari Laundry status)
- Laporan (dari Report)

## 3. Pengguna dan Hak Akses

Hanya dua role yang punya akun: **Admin** dan **Pegawai/Kasir**. Pelanggan adalah pihak eksternal tanpa akun.

| Fitur | Admin | Pegawai/Kasir |
|---|:-:|:-:|
| Login, lihat dashboard | ✓ | ✓ |
| Kelola akun pegawai | ✓ | |
| Kelola layanan dan tarif | ✓ | |
| Tambah dan edit pelanggan | ✓ | ✓ |
| Hapus pelanggan | ✓ | |
| Buat transaksi (+ cetak nota) | | ✓ |
| Edit transaksi | ✓ | ✓ |
| Batalkan transaksi | ✓ | |
| Catat pembayaran (tunai/QRIS) | | ✓ |
| Update status cucian | | ✓ |
| Lihat semua transaksi | ✓ | |
| Laporan pendapatan | ✓ | |

Catatan:
- Keputusan yang sudah pasti: **pegawai boleh edit transaksi, pembatalan hanya admin**, dan semua aksi dicatat di `log_aktivitas`.
- **[ASUMSI]** Kasir boleh tambah dan edit pelanggan, hapus pelanggan hanya admin.
- **[ASUMSI]** Admin tidak membuat transaksi, mencatat pembayaran, atau mengubah status (sesuai spesifikasi awal). Bisa dilonggarkan kalau perlu.
- Pengecekan hak akses dilakukan lewat middleware Auth & role.

## 4. Alur Utama Transaksi

1. **Login.** Pegawai/admin login, sistem cek akun di `users`, menu ditampilkan sesuai role.
2. **Input pelanggan.** Kasir mencari pelanggan lewat nomor HP. Jika belum ada, buat baru. `no_hp` unik agar tidak dobel.
3. **Input cucian.** Kasir memilih layanan dan mengisi jumlah (kg atau pcs sesuai satuan layanan). Satu transaksi bisa berisi banyak layanan.
4. **Hitung biaya.** `subtotal = jumlah × tarif`. `total_biaya = jumlah semua subtotal`. Tarif saat itu disimpan di detail (`tarif_saat_itu`) supaya perubahan harga tidak mengubah transaksi lama.
5. **Simpan transaksi dan cetak nota.** Sistem membuat `no_transaksi` unik, menyimpan transaksi dengan status awal **Diterima** dan **Belum Lunas**, mencatat status awal di `riwayat_status`, lalu menampilkan nota.
6. **Pembayaran.** Bisa saat cucian diterima atau saat diambil. DP diperbolehkan. Lihat bagian 5.
7. **Proses laundry.** Pegawai memperbarui status cucian. Lihat bagian 6.
8. **Pengambilan.** Pelanggan mengambil cucian. Jika belum lunas, dilunasi dulu. Status menjadi **Diambil**.

Contoh simulasi (angka hanya contoh): cuci + setrika 5 kg × Rp8.000 = Rp40.000, bayar tunai, status bayar Lunas, status cucian Diproses.

## 5. Alur Pembayaran

- Metode: **tunai** atau **QRIS statis**.
- Satu transaksi bisa punya **lebih dari satu pembayaran** (misal DP lalu pelunasan). Kolom `jenis`: `DP` atau `Pelunasan`.
- **Tunai:** kasir menerima uang, input `jumlah_bayar`, simpan.
- **QRIS statis:**
  1. Outlet punya satu gambar QRIS (file di storage Laravel, ditampilkan di halaman pembayaran, tidak butuh tabel).
  2. Kasir memilih metode QRIS, gambar QRIS ditampilkan di layar, pelanggan scan dan bayar.
  3. Kasir mengecek manual di HP bahwa uang sudah masuk.
  4. Kasir klik "konfirmasi" dan mengisi `no_referensi` dari bukti bayar. Pembayaran tersimpan dengan `metode = qris`.
- **Status bayar** di `transaksi` menjadi **Lunas** jika total pembayaran sudah sama dengan `total_biaya`, selain itu **Belum Lunas**.
- Tidak ada diskon atau potongan harga.
- Bukan QRIS dinamis / payment gateway. Itu hanya pengembangan lanjutan, di luar ruang lingkup.

## 6. Status

| Jenis | Nilai |
|---|---|
| `status_cucian` | Diterima → Diproses → Selesai → Diambil |
| `status_bayar` | Belum Lunas / Lunas |
| `status_transaksi` | Aktif / Batal (batal wajib alasan di `alasan_batal`) |

- Setiap perubahan status cucian ditambahkan sebagai baris baru di `riwayat_status` (siapa, kapan, status apa), dan `transaksi.status_cucian` diperbarui ke nilai terakhir.
- Transaksi yang dibatalkan **tidak dihapus** dari database.

## 7. Batal dan Edit Transaksi

- Edit: boleh admin dan pegawai.
- Batal: hanya admin, dengan alasan.
- Setiap edit dan pembatalan dicatat di `log_aktivitas` (`aksi`, `keterangan`, `waktu`, `id_user`, `id_transaksi`).

## 8. Laporan

- Rekap transaksi dan pendapatan, hanya untuk admin.
- Filter: harian, bulanan, per pegawai.
- Sumber data: `transaksi` (tanggal, pegawai) dan `pembayaran` (uang yang masuk).
- Ada fitur export (format belum ditentukan).

## 9. Skema Database (8 tabel)

Penamaan kolom: primary key diberi nama `id_<nama entitas>` (misal `id_transaksi`, bukan `id`), dan foreign key memakai **nama yang sama persis** dengan primary key yang dirujuk (misal `transaksi.id_pelanggan` merujuk `pelanggan.id_pelanggan`). Semua PK dan FK bertipe `bigint`. Format DBML (bisa ditempel ke dbdiagram.io):

```dbml
Table users {
  id_user bigint [pk, increment]
  nama varchar(100) [not null]
  username varchar(50) [not null, unique]
  password varchar(255) [not null]
  role varchar(20) [not null, note: 'admin / pegawai']
  is_aktif boolean [not null, default: true]
}

Table pelanggan {
  id_pelanggan bigint [pk, increment]
  nama varchar(100) [not null]
  no_hp varchar(20) [unique, note: 'unik, biar pelanggan tidak dobel']
  alamat text
}

Table layanan {
  id_layanan bigint [pk, increment]
  nama_layanan varchar(100) [not null]
  satuan varchar(10) [not null, note: 'kg / pcs']
  tarif decimal(10,2) [not null]
  is_aktif boolean [not null, default: true, note: 'layanan nonaktif tidak dihapus']
}

Table transaksi {
  id_transaksi bigint [pk, increment]
  no_transaksi varchar(30) [not null, unique]
  id_pelanggan bigint [not null]
  id_user bigint [not null, note: 'pegawai penerima cucian']
  tgl_terima datetime [not null]
  estimasi_selesai datetime
  total_biaya decimal(12,2) [not null, default: 0]
  status_cucian varchar(20) [not null, default: 'Diterima', note: 'Diterima / Diproses / Selesai / Diambil']
  status_bayar varchar(20) [not null, default: 'Belum Lunas', note: 'Belum Lunas / Lunas']
  status_transaksi varchar(20) [not null, default: 'Aktif', note: 'Aktif / Batal']
  alasan_batal text
}

Table detail_transaksi {
  id_detail bigint [pk, increment]
  id_transaksi bigint [not null]
  id_layanan bigint [not null]
  jumlah decimal(8,2) [not null, note: 'kg atau pcs sesuai satuan layanan']
  tarif_saat_itu decimal(10,2) [not null, note: 'tarif saat transaksi dibuat']
  subtotal decimal(12,2) [not null]
}

Table pembayaran {
  id_pembayaran bigint [pk, increment]
  id_transaksi bigint [not null]
  id_user bigint [not null, note: 'kasir yang mencatat']
  tgl_bayar datetime [not null]
  jumlah_bayar decimal(12,2) [not null]
  metode varchar(10) [not null, note: 'tunai / qris']
  no_referensi varchar(50) [note: 'diisi untuk QRIS, kosong jika tunai']
  jenis varchar(15) [not null, note: 'DP / Pelunasan']
}

Table riwayat_status {
  id_riwayat bigint [pk, increment]
  id_transaksi bigint [not null]
  id_user bigint [not null]
  status varchar(20) [not null]
  waktu datetime [not null]
  catatan text
}

Table log_aktivitas {
  id_log bigint [pk, increment]
  id_user bigint [not null]
  id_transaksi bigint
  aksi varchar(20) [not null, note: 'edit / batal']
  keterangan text
  waktu datetime [not null]
}

Ref: transaksi.id_pelanggan > pelanggan.id_pelanggan
Ref: transaksi.id_user > users.id_user
Ref: detail_transaksi.id_transaksi > transaksi.id_transaksi
Ref: detail_transaksi.id_layanan > layanan.id_layanan
Ref: pembayaran.id_transaksi > transaksi.id_transaksi
Ref: pembayaran.id_user > users.id_user
Ref: riwayat_status.id_transaksi > transaksi.id_transaksi
Ref: riwayat_status.id_user > users.id_user
Ref: log_aktivitas.id_user > users.id_user
Ref: log_aktivitas.id_transaksi > transaksi.id_transaksi
```

Catatan teknis:
- Tipe data di atas ditentukan sendiri (bukan dari dosen), boleh disesuaikan.
- Tabel `users` tidak sama dengan tabel bawaan Laravel (tidak ada `email`, dll.). Sesuaikan saat membuat migration.
- `total_biaya`, `status_bayar`, dan `status_cucian` di `transaksi` adalah **data turunan yang sengaja disimpan** (denormalisasi) demi kemudahan laporan dan tampilan. Aplikasi harus memperbaruinya bersamaan dengan tabel sumbernya (`detail_transaksi`, `pembayaran`, `riwayat_status`), idealnya dalam satu database transaction.
- `jumlah` bertipe desimal karena berat bisa pecahan (misal 2,5 kg), sedangkan pcs berupa bilangan bulat.
- `no_hp` unik. Di MySQL kolom unik boleh NULL berkali-kali.
- Model Eloquent: `User`, `Pelanggan`, `Layanan`, `Transaksi`, `DetailTransaksi`, `Pembayaran`, `RiwayatStatus`, `LogAktivitas`.
- Karena PK bukan bernama `id`, di migration pakai `$table->id('id_transaksi')` dan di model set `protected $primaryKey = 'id_transaksi';`. Untuk FK, tentukan kolom tujuan secara eksplisit, misal `$table->foreignId('id_pelanggan')->constrained('pelanggan', 'id_pelanggan');`. Relasi Eloquent (`belongsTo`, `hasMany`) juga perlu menyebut nama foreign key dan owner key-nya.

## 10. ERD (Notasi Chen)

Entitas (8): Pelanggan, Transaksi, Detail Transaksi, Layanan, Pembayaran, Riwayat Status, Log Aktivitas, User. Satu entitas = satu tabel.

| Relasi | Entitas A | Entitas B |
|---|---|---|
| Menitipkan | Pelanggan (1) | Transaksi (N) |
| Menerima | User (1) | Transaksi (N) |
| Terdiri dari | Transaksi (1) | Detail Transaksi (N) |
| Dipilih pada | Layanan (1) | Detail Transaksi (N) |
| Dibayar | Transaksi (1) | Pembayaran (N) |
| Mencatat | User (1) | Pembayaran (N) |
| Memiliki | Transaksi (1) | Riwayat Status (N) |
| Mengubah | User (1) | Riwayat Status (N) |
| Tercatat | Transaksi (1) | Log Aktivitas (N) |
| Melakukan | User (1) | Log Aktivitas (N) |

- **Detail Transaksi** digambar sebagai entitas asosiatif (kotak sendiri), karena hubungan Transaksi dan Layanan adalah banyak-ke-banyak. Atributnya: `id_detail` (PK), `jumlah`, `tarif_saat_itu`, `subtotal`, `id_transaksi` (FK), `id_layanan` (FK). Dengan cara ini jumlah entitas di ERD (8) sama dengan jumlah tabel (8).
- Foreign key **ditulis sebagai atribut** (oval putus-putus, label `(FK)`) di entitas sisi "banyak": Transaksi (`id_pelanggan`, `id_user`), Pembayaran (`id_transaksi`, `id_user`), Riwayat Status (`id_transaksi`, `id_user`), Log Aktivitas (`id_user`, `id_transaksi`). Detail Transaksi membawa `id_transaksi` dan `id_layanan` (FK). Primary key (`id_...`) digarisbawahi.
- Kardinalitas ditulis 1 dan N (N = banyak). Tidak ada relasi banyak-ke-banyak langsung karena sudah dipecah lewat Detail Transaksi.
- **Admin dan pegawai bukan dua entitas.** Keduanya adalah entitas **User** yang dibedakan oleh atribut `role` (admin / pegawai). Role tidak membuat relasi baru. Pembatasan siapa boleh apa dijaga di aplikasi (middleware), bukan di ERD.

## 11. Use Case

Aktor: **Admin** dan **Pegawai (kasir)**. Pelanggan tidak digambar sebagai aktor karena tidak berinteraksi langsung dengan sistem.

**Dipakai kedua role:** Login, Lihat dashboard, Kelola pelanggan (tambah dan edit), Edit transaksi.

**Khusus Admin:** Kelola akun pegawai, Kelola layanan dan tarif, Hapus pelanggan, Batalkan transaksi, Lihat semua transaksi, Lihat laporan pendapatan.

**Khusus Pegawai:** Buat transaksi, Catat pembayaran (tunai/QRIS), Update status cucian.

**Use case pendukung (tanpa garis ke aktor):**

| Relasi | Dari | Ke |
|---|---|---|
| `<<include>>` | Buat transaksi | Hitung total biaya |
| `<<include>>` | Buat transaksi | Cetak nota |
| `<<include>>` | Edit transaksi | Catat log aktivitas |
| `<<include>>` | Batalkan transaksi | Catat log aktivitas |
| `<<extend>>` | Bayar QRIS | Catat pembayaran |

Aturan: include = selalu dijalankan (panah dari use case utama ke yang disertakan). Extend = opsional, hanya jika kondisi terpenuhi (panah dari tambahan ke use case utama). UML hanya mengenal include dan extend.

## 12. Di Luar Ruang Lingkup

- Pemesanan online dan akun pelanggan
- Payment gateway / QRIS dinamis dengan konfirmasi otomatis
- Diskon dan potongan harga
- Pencatatan uang diterima dan kembalian tunai (hanya nominal yang dibayar yang disimpan)

## 13. Hal yang Masih Terbuka

Belum diputuskan. Tanyakan ke pengguna sebelum mengimplementasikan:

1. `no_hp` pelanggan wajib diisi atau boleh kosong.
2. Apakah ada fitur **Cetak ulang nota** yang berdiri sendiri (saran: ya, tapi belum disetujui).
3. Format dan jenis export laporan (PDF, Excel, dll.).
4. Format penomoran `no_transaksi`.
5. Batasan edit transaksi (misal apakah masih boleh diedit setelah status Diambil).
6. Apakah admin juga boleh membuat transaksi dan mencatat pembayaran.

## 14. Panduan untuk AI Agent

- Bahasa kerja dengan pengguna: Bahasa Indonesia santai.
- Patuhi nama tabel, kolom, dan status persis seperti dokumen ini.
- Jangan menambah tabel atau fitur baru tanpa konfirmasi.
- Jangan memakai nama-nama yang tidak ada di dokumen, misalnya relasi UML `exclude` (tidak ada dalam UML).
- Jika ada konflik antara dokumen ini dan permintaan baru dari pengguna, ikuti permintaan baru lalu tandai bagian dokumen yang perlu diperbarui.
