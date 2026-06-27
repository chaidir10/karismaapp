# TODO - Perbaikan Lokasi Halaman Riwayat Pegawai

- [x] Analisis perbedaan mekanisme deteksi alamat antara `resources/views/pegawai/dashboard.blade.php` (riwayat hari ini) dan `resources/views/pegawai/riwayat.blade.php`.
- [x] Samakan mekanisme inisialisasi detail map/alamat di `resources/views/pegawai/riwayat.blade.php` agar stabil seperti dashboard.
- [x] Pastikan trigger inisialisasi berjalan pada load halaman (DOMContentLoaded + fallback turbo) dan geocoding tidak stuck loading.
- [ ] Verifikasi perubahan script agar alamat muncul untuk status approved (pakai `wilayahAlamat`) dan selain approved pakai reverse geocode.
