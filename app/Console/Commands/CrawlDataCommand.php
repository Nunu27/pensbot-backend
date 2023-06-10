<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Models\daftar_ulang_simandiri;
use App\Models\daftar_ulang_snbp;
use App\Models\daftar_ulang_snbt;


class CrawlDataCommand extends Command
{
    protected $signature = 'crawl:data';
    protected $description = 'Crawl data from pmb.pens.ac.id and save to database';

    public function handle()
{
    // Buat HTTP client dengan Guzzle
    $client = new Client();
    $response = $client->get('https://pmb.pens.ac.id/index.php/simandiri/');

    // Proses response dan simpan data ke database
    $html = $response->getBody()->getContents();
    // Buat instance dari DOMCrawler
    $crawler = new Crawler($html);
    $groupJadwal = $crawler->filter('.jadwal-pelaksanaan');
    // Ambil elemen yang mengandung data jadwal pelaksanaan seleksi mandiri
    $jadwalElement = $crawler->filter('.jadwal-pelaksanaan');

    // Parsing data dari elemen jadwal pelaksanaan
    $keterangan = $jadwalElement->filter('.keterangan')->text();
    $tanggalMulai = $jadwalElement->filter('.tanggal-mulai')->text();
    $tanggalSelesai = $jadwalElement->filter('.tanggal-selesai')->text();

    // Simpan data ke dalam model daftar_ulang_simandiri
    $data = [
        'keterangan' => $keterangan,
        'tanggal_mulai' => $tanggalMulai,
        'tanggal_selesai' => $tanggalSelesai,
    ];

    // daftar_ulang_simandiri::create($data);
    $this->info('Data crawled and saved successfully.');
    }
}
