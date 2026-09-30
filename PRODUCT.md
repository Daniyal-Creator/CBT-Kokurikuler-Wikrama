# Product

## Register

brand

## Users

- **Publik sekolah**: murid, guru, dan orang tua. Melihat halaman utama tanpa login, baik di layar besar (TV/proyektor di lobi atau kelas, dibaca dari 3 sampai 5 meter) maupun di laptop dan ponsel dari dekat. Kedua konteks sama penting.
- **Petugas**: murid anggota tim kokurikuler yang mencatat Setoran Jelantah dan Timbangan Sampah dari laptop di kelas, setelah kegiatan selesai. Butuh input cepat berbaris-baris.
- **Guru dan Admin**: memverifikasi catatan dan mengelola data lewat panel Filament.

Pekerjaan utama halaman publik: menjawab "kelas mana yang paling banyak mengumpulkan jelantah?" dan "apakah sampah sekolah berkurang?" dalam sekali lihat.

## Product Purpose

Aplikasi pencatatan kegiatan P5 / kokurikuler bertema gaya hidup berkelanjutan di SMK Wikrama: Setoran Jelantah (minyak goreng bekas) per kelas dan Pemilahan Sampah harian untuk seluruh sekolah. Berhasil bila data terus diisi setelah minggu-minggu pertama, dan murid melihat botol jelantah mereka berubah menjadi angka rupiah yang nyata.

## Brand Personality

**Jujur, buatan sekolah, bangga.** Terasa seperti papan pengumuman sekolah yang dirawat baik: kertas daur ulang, label botol, cap stempel, huruf tebal yang terbaca dari jauh. Bukan startup, bukan kampanye korporat. Nada bahasa Indonesia yang lugas dan hangat, bicara kepada murid sebagai pelaku, bukan penonton.

## Anti-references

- Warna gradasi dalam bentuk apa pun (latar, teks, tombol, grafik). Tampilan "AI-generated" yang mengilap.
- Template SaaS: hero di tengah, tiga kartu ikon identik, angka metrik besar dengan aksen gradasi.
- Estetika "eco" generik: daun hijau neon, emerald/teal cerah, ikon bola dunia, stok foto tangan memegang tunas.
- Glassmorphism dan bayangan melayang.
- Data sampah yang disajikan seperti kompetisi. Pemilahan Sampah bukan lomba; angka besar di sana justru kabar buruk.

## Design Principles

1. **Terbaca dari lobi.** Setiap angka penting harus terbaca dari beberapa meter di layar proyektor yang pudar. Ukuran dan kontras mengalahkan dekorasi.
2. **Benda nyata, bukan simbol.** Visual diambil dari benda kegiatan itu sendiri: gelas takar liter, botol, stempel, kertas bekas. Hindari ikon generik "lingkungan".
3. **Jelantah adalah lomba, sampah bukan.** Kedua kegiatan sengaja tidak simetris dan tampil dengan bahasa visual berbeda.
4. **Jujur soal data.** Hanya catatan terverifikasi yang dihitung, hari kosong berarti tidak diketahui, dan fitur yang belum jalan dikatakan belum jalan.
5. **Tanpa nama murid.** Halaman publik hanya memuat agregat.

## Accessibility & Inclusion

- Target WCAG 2.2 AA; kontras teks utama sebaiknya melebihi AA karena proyektor menurunkan kontras.
- Warna tidak pernah menjadi satu-satunya pembawa makna (peringkat juga diberi nomor, grafik diberi label).
- Hormati `prefers-reduced-motion`.
- Huruf badan dipilih karena keterbacaan (Atkinson Hyperlegible), bukan gaya.
