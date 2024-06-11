<?php

namespace App\Http\Controllers\Handlers;

class InformasiPendaftaranHandler extends Handler
{
    public function kuotaPendaftaran($payload)
    {
        return "PENS memiliki 13 program studi, dimana masing-masing terdiri dari 2 kelas. Per kelas kuotanya adalah 30 mahasiswa. Detail daya tampung atau kuota mahasiswa baru PENS 2024 dapat diakses melalui link berikut https://pmb.pens.ac.id/index.php/dayatampung/\n";
    }
    public function daftarUlang($payload)
    {
        // TODO: scrape tanggal
        return "
Hi! Berikut adalah timeline daftar ulang *SNBP*, *SNBT*, dan *SIMANDIRI* :
*SNBP* 29 Maret s.d. 14 April 2024
*SNBT* 21 Juni s.d. 07 Juli 2024
*SIMANDIRI* 17 s.d. 23 Juli 2024

Untuk informasi lebih lanjut silahkan unduh kalender akademik PENS: https://intip.in/KalenderAkademikPENS

Berikut adalah tata cara daftar ulang mahasiswa baru PENS antara lain :
1. Buka laman http://pmb.pens.ac.id/daftarulang kemudian masukkan No. pendaftaran dan tanggal lahir Anda kemudian klik login
2. Klik kode pembayaran bank Mandiri
3. Anda akan mendapatkan informasi kode transaksi pembayaran Uang Kuliah Tunggal  (UKT) Bank Mandiri, silahkan anda print informasi tersebut. Bayar biaya pendidikan anda melalui ATM, Internet Banking ataupun Teller Bank Mandiri Terdekat di seluruh Indonesia
4. Bilamana anda sudah melunasi pembayaran UKT, maka login di site http://pmb.pens.ac.id/daftarulang, maka secara otomatis aplikasi menuju form Isian Data Mahasiswa Baru, Entri secara lengkap Biodata Pribadi Anda, Lalu klik tombol Simpan
5. Lihat dan Cetak Form Biodata, Form Pernyataan Bebas Narkoba, Form Mentaati Peraturan, Form Pembuatan KTM dan Form Pernyataan Pembayaran, dengan klik masing-masing link
6. Selesai Anda Menjadi Mahasiswa PENS
7. Datang Ke BAAK PENS sesuai pengumuman untuk penyerahan berkas
8. Apabila kesulitan, silahkan hubungi BAAK PENS atau email ke eis@pens.ac.id
";
    }
    public function SNBP($payload)
    {
        return "
Halo! Berikut adalah tata cara pendaftaran SNBP PENS :
1. Registrasi Akun SNPMB 2024
- Pembuatan Akun SNPMB bagi Sekolah dan Siswa dimulai pada 09 Januari 2024, pukul 15.00 WIB pada laman Portal SNPMB.
- Sekolah dan Siswa yang sudah mengikuti uji coba kedua/lanjutan pembuatan Akun SNPMB dan berhasil, tidak perlu membuat akun SNPMB lagi.
- Pembuatan Akun SNPMB berakhir pada: 09 Februari 2024 bagi Sekolah, dan 15 Februari 2024 bagi Siswa.
2. Login terlebih dahulu melalui portal SNPMB atau https://snpmb.bppp.kemdikbud.go.id Persyaratan siswa yang diperbolehkan masuk ke aplikasi SNBP 2024 :
- Sudah memiliki akun SNPMB yang berstatus permanen.
- Merupakan siswa eligible yang telah diisikan nilainya secara lengkap dan difinalisasi melalui aplikasi PDSS oleh sekolah.
- Merupakan siswa lulusan 2024.
3. Validasi data profil
4. Pemilihan program studi (prodi)
5. Pengisian portofolio
6. Pengisian prestasi
7. Finalisasi. Siswa hanya bisa melakukan finalisasi jika sudah mengisi jumlah tanggungan, memilih prodi, dan mengunggah portofolio (hanya untuk prodi seni dan olahraga).
";
    }
    public function SNBT($payload)
    {
        return "
Halo! Berikut adalah cara daftar UTBK-SNBT 2024 antara lain :
1. Membuka laman portal-snpmb.bppp.kemdikbud.go.id di perangkat kamu miliki.
2. Login menggunakan akun SNPMB yang telah dibuat sebelumnya.
3. Memilih menu verifikasi dan validasi data.
4. Mengisi serta melengkapi biodata, unggah foto berwarna terbaru, verifikasi biodata serta unduh dan mengunggah pernyataan tunanetra/low vision.
5. Memilih menu pendaftaran UTBK-SNBT.
6. Memilih program studi, unggah portofolio jika diperlukan, pilih pusat UTBK PTN, dan dapatkan slip pembayaran biaya UTBK.
7. Melakukan pembayaran biaya UTBK sesuai nominal tertera.
8. Unduh dan cetak kartu peserta UTBK.
";
    }
    public function SIMANDIRI($payload)
    {
        return "
Halo! Berikut adalah tata cara pendaftaran SIMANDIRI PENS :
1. Pendaftaran Dibuka Mulai Tanggal 29-05-2024 Jam 00:00:00 WIB
2. UTBK Online dirumah masing-masing, siapkan browser internet dan aplikasi zoom meeting, serta wajib menggunakan WebCam
3. Tata Cara UTBK Online akan diumumkan melalui laman http://pmb.pens.ac.id
4. Cetak Ulang Kartu Peserta, Klik link Cetak Kartu Pendaftaran
5. Biaya Pendaftaran : Rp. 300.000, Biaya Pendaftaran Yang Sudah Ditransfer Tidak Bisa Ditarik     Dengan Alasan Apapun
6. NISN Terdaftar di Kemendikbud dan Hanya diperkenankan 1x pendaftaran
7. Bila Lolos Seleksi, Wajib Membayar UKT/Semester
    Rp 500.000 sd Rp 10.000.000     
8. Bila Lolos Seleksi, Wajib Membayar IPI (Sekali Bayar)
    Min Rp 3.000.000 sd Maks Rp 40.000.000     
9. Pendaftaran Gunakan Browser Chrome Atau Mozilla Firefox Versi PC

\* Cetak Kartu Pendaftaran bila selesai registrasi final, ditutup sd tanggal :08-07-2024 Jam 23:00:00 WIB
\*\* Registrasi dan pembayaran PMB SIMANDIRI ditutup tanggal 23-06-2024 jam 16:00:00 WIB
\*\* Entri Data dan Finalisasi PMB SIMANDIRI ditutup tanggal 23-06-2024 jam 23:45:00 WIB
Segala permasalahan atau pertanyaan dapat menghubungi Call Center 031-594 7280 Ext 7101 atau email ke pmb@pens.ac.id pada hari dan jam kerja
";
    }
}
