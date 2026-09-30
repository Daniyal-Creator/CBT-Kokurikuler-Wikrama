# CONTEXT

Bahasa domain untuk aplikasi pencatatan kegiatan P5 / kokurikuler sekolah.
Dokumen ini adalah **glosarium** — bukan spesifikasi, bukan catatan implementasi.
Kalau sebuah kalimat di bawah menyebut tabel, kolom, atau framework, kalimat itu salah tempat.

## Status

Fase 1 mencakup dua kegiatan: **Setoran Jelantah** dan **Pemilahan Sampah**.
**Perawatan Tanaman** sudah dikenali sebagai bagian domain, tetapi sengaja belum dimodelkan.

Kedua kegiatan itu **tidak simetris**, dan perbedaannya adalah hal terpenting di dokumen ini:

| | Jelantah | Pemilahan Sampah |
|---|---|---|
| Siapa yang berkontribusi | Siswa, atas nama Kelasnya | Sekolah sebagai satu kesatuan |
| Unit pencatatan | Siswa | Sekolah, per hari |
| Untuk apa datanya | Perbandingan antar-Kelas | Pemantauan naik-turunnya sampah dari hari ke hari |

Memaksa keduanya menjadi satu bentuk yang seragam adalah kesalahan yang sudah pernah dibuat
dan sengaja dibatalkan.

---

## Istilah inti

**Projek**
Satu penyelenggaraan P5 yang dibatasi waktu, mis. *"Gaya Hidup Berkelanjutan — Semester Ganjil 2025/2026"*.
Setiap catatan kegiatan hidup di dalam tepat satu Projek. Tanpa Projek, sebuah catatan tidak punya arti —
angka tanpa periode tidak bisa dibandingkan, dan papan peringkat tanpa batas waktu tidak pernah berakhir.

**Tahun Ajaran**
Rentang waktu administratif sekolah. Menentukan penempatan Siswa di sebuah Kelas.
Berbeda dari Projek: satu Tahun Ajaran dapat memuat beberapa Projek.

**Kelas**
Rombongan belajar, mis. *8A*. Kelas adalah unit perbandingan **untuk Jelantah saja**.
Pemilahan Sampah tidak mengenal Kelas.

**Siswa**
Seorang murid. Diidentifikasi oleh **NIS**. Penempatan Siswa pada sebuah Kelas berlaku
**per Tahun Ajaran**, bukan melekat selamanya pada Siswa — murid naik kelas, rekap tahun lalu harus tetap benar.

**Petugas**
Siswa anggota tim kokurikuler yang diberi wewenang mencatat data kegiatan — baik Setoran Jelantah
di hari pengumpulan maupun Timbangan Sampah di sore hari. Satu peran, bukan dua.
Petugas adalah **peran yang sedang dipegang**, bukan sifat permanen seseorang.

Kelas asal seorang Petugas **tidak ada hubungannya** dengan Kelas yang menerima kontribusi:
Petugas mencatat atas nama sekolah, bukan atas nama kelasnya sendiri.

**Jadwal Piket**
Pembagian hari bertugas antar tim kokurikuler. Bersifat **keterangan**, bukan pembatas —
ia menjelaskan siapa yang seharusnya bertugas, tanpa menghalangi orang lain menggantikan
ketika yang dijadwalkan berhalangan.

**Guru**
Pengguna yang memverifikasi catatan dan melihat rekap. Satu-satunya pihak yang dapat mengubah
status catatan menjadi terverifikasi, membatalkan verifikasi, atau mengoreksi angka yang sudah terverifikasi.

---

## Kegiatan 1 — Jelantah

**Jelantah**
Minyak goreng bekas yang dibawa Siswa dari rumah ke sekolah.

**Setoran**
Satu peristiwa seorang Siswa menyerahkan Jelantah pada suatu tanggal.
Dicatat dalam **liter** — satuan yang dipahami manusia yang menakarnya.
Berat dalam kilogram bukan data yang dicatat, melainkan **hasil hitungan** dari liter;
menyimpan keduanya akan menciptakan dua kebenaran yang bisa saling bertentangan.

**Stok Jelantah**
Jelantah yang sudah diterima sekolah tetapi belum dijual.
Bukan angka yang diketik siapa pun, melainkan **turunan**: seluruh Setoran terverifikasi dikurangi seluruh Penjualan.

**Penjualan**
Peristiwa sekolah melepas Jelantah kepada pihak luar (pengepul) dengan imbalan uang.
Sisi "keluar" dari Jelantah. Tanpa ini, Jelantah yang masuk lenyap ke dalam lubang tanpa penjelasan.

**Papan Peringkat**
Urutan Kelas berdasarkan Jelantah yang terkumpul dalam satu Projek.
Hanya menyangkut Jelantah — hasil Pemilahan Sampah tidak pernah diperingkatkan.

---

## Kegiatan 2 — Pemilahan Sampah

**Pemilahan**
Kegiatan di penghujung hari sekolah: sampah dari **seluruh tong sampah sekolah** dikumpulkan,
dipisahkan menurut jenisnya, lalu ditimbang. Sekolah memiliki timbangan, sehingga satuannya
adalah **kilogram** — bukan taksiran jumlah kantong.

**Jenis Sampah**
Kategori sampah yang dapat ditimbang, mis. *Organik, Plastik, Kertas, Logam, Kaca, Residu*.
Daftarnya **ditentukan sekolah, bukan oleh program** — sekolah boleh menambah atau menghentikan
sebuah jenis tanpa perlu kode diubah.

**Timbangan**
Satu catatan hasil menimbang: pada satu tanggal, untuk satu Jenis Sampah, sekian kilogram
**untuk seluruh sekolah**. Tidak ada Kelas di dalamnya, dan tidak ada yang dimenangkan olehnya.

**Tren Sampah**
Naik-turunnya jumlah sampah dari hari ke hari, per Jenis Sampah.
Inilah satu-satunya alasan data Pemilahan dikumpulkan: melihat apakah sampah sekolah berkurang.
Angka besar di sini bukan prestasi — justru sebaliknya.

---

## Istilah lintas kegiatan

**Catatan**
Sebutan umum untuk Setoran maupun Timbangan. Keduanya berbagi satu siklus hidup yang sama.

**Status**
Tahap sebuah Catatan: *diajukan*, lalu *terverifikasi* atau *ditolak* oleh Guru.
Hanya Catatan terverifikasi yang dihitung ke dalam rekap, Stok, dan Papan Peringkat.
Status adalah bagian inti dari domain, bukan hiasan — tanpanya, papan peringkat mengundang karangan angka.

**Jejak Perubahan**
Riwayat siapa mengubah Catatan yang sudah terverifikasi, kapan, dan dari nilai berapa ke berapa.
Ada supaya koreksi tetap mungkin tanpa membuat verifikasi kehilangan makna.

---

## Istilah yang sengaja dihindari

**"Daur ulang"** — pernah dipakai untuk menyebut dua hal sekaligus: kegiatan Pemilahan Sampah,
dan nasib Jelantah setelah Penjualan. Gunakan **Pemilahan** atau **Penjualan** secara eksplisit.

**"Sampah"** tanpa keterangan — dapat berarti Jenis Sampah (kategorinya) atau Timbangan (hasil menimbangnya).

**"Setoran"** — dicadangkan khusus untuk Jelantah. Hasil Pemilahan disebut **Timbangan**, bukan setoran.

**"Poin"** — sempat diusulkan sebagai nilai penyetara antara liter Jelantah dan kilogram sampah.
Sejak Pemilahan Sampah dinyatakan bukan kompetisi, tidak ada lagi dua satuan yang perlu disetarakan,
sehingga istilah ini **dihapus**. Peringkat Kelas dinyatakan langsung dalam liter Jelantah.
