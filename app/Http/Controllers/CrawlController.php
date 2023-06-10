<?php

namespace App\Http\Controllers;

use App\Models\daftar_ulang_simandiri;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class CrawlController extends Controller
{
    public function index()
    {
        // Buat HTTP client dengan Guzzle
        $client = new Client();
        $response = $client->get('https://pmb.pens.ac.id/index.php/simandiri/');

        // Proses response dan simpan data ke database
        $html = $response->getBody()->getContents();

        $crawler = new Crawler($html);

        $data = [];

        // $crawler->filter('.et_pb_text_inner')->each(function ($row) use (&$data) {
        //     $rowData = [];

        //     $row->filter('.et_pb_column.et_pb_column_1_2.et_pb_column_8')->each(function ($column1, $index) use (&$rowData) {
        //         $column1->filter('.et_pb_module.et_pb_text')->each(function ($module, $index) use (&$rowData) {
        //             $text = $module->filter('.et_pb_text_inner')->text();
        //             $rowData["column1"][$index] = $text;
        //         });
        //     });

        //     var_dump($rowData);

        //     $row->filter('.et_pb_column.et_pb_column_1_2.et_pb_column_9')->each(function ($column2, $index) use (&$rowData) {
        //         $column2->filter('.et_pb_module.et_pb_text')->each(function ($module, $index) use (&$rowData) {
        //             $text = $module->filter('.et_pb_text_inner')->text();
        //             $dateParts = explode(" – ", $text);

        //             $startDate = trim($dateParts[0]);
        //             // dd($startDate);

        //             $endDate = isset($dateParts[1]) ? trim($dateParts[1]) : null;
        //             $startDate = date("Y-m-d", strtotime($startDate));
        //             if($endDate != null)
        //                 $endDate = date("Y-m-d", strtotime($endDate));
        //             $rowData["column2"]["tanggal-mulai"][$index] = $startDate;
        //             $rowData["column2"]["tanggal-selesai"][$index] = $endDate;
        //         });
        //     });

        //     // dd($rowData);

        //     $data[] = $rowData;
        // });

        $crawler->filter('.et_pb_text_inner')->each(function ($row) use (&$data) {
            $rowData = [];

            $column1Texts = $row->filter('.et_pb_column.et_pb_column_1_2.et_pb_column_8 .et_pb_module.et_pb_text .et_pb_text_inner')->each(function ($module) {
                return $module->text();
            });

            $rowData['column1'] = $column1Texts;

            $column2Dates = $row->filter('.et_pb_column.et_pb_column_1_2.et_pb_column_9 .et_pb_module.et_pb_text .et_pb_text_inner')->each(function ($module) {
                $text = $module->text();
                $dateParts = explode(" – ", $text);
                $startDate = trim($dateParts[0]);
                $endDate = isset($dateParts[1]) ? trim($dateParts[1]) : null;
                $startDate = date("Y-m-d", strtotime($startDate));
                if ($endDate != null) {
                    $endDate = date("Y-m-d", strtotime($endDate));
                }

                return [
                    'tanggal-mulai' => $startDate,
                    'tanggal-selesai' => $endDate,
                ];
            });

            $rowData['column2'] = $column2Dates;

            $data[] = $rowData;
        });

        // var_dump($data);

        // dd($data);
        $result = [];
        foreach ($data[0]['column1'] as $index => $item) {

            // Buat array baru dengan struktur yang diinginkan
            $newItem = [
                // "keterangan" => $data[0]['column1'][$index],
                "keterangan" => $item,
                "tanggal-mulai" => $data[0]["column2"]["tanggal-mulai"][$index],
                "tanggal-selesai" => $data[0]["column2"]["tanggal-selesai"][$index],
            ];

            // Tambahkan ke array hasil
            $result[] = $newItem;
        }

        dd($result);

        try {
            foreach ($result as $key => $val) {
                daftar_ulang_simandiri::create(
                    [
                        'keterangan' => $val["keterangan"],
                        'tanggal_mulai' => $val["tanggal-mulai"],
                        'tanggal_selesai' => $val["tanggal-selesai"],
                    ]
                );
            }
            return response()->json("Berhasil");
        } catch (\Exception $e) {
            return response()->json($e);
        }
    }

    public function snbp()
    {
        $url = 'https://pmb.pens.ac.id/index.php/snbp/';

        // Menggunakan Guzzle untuk mengambil konten halaman web
        $client = new Client();
        $response = $client->get($url);
        $html = $response->getBody()->getContents();
        // dd($html);
        // Membuat objek Crawler dari konten HTML
        $crawler = new Crawler($html);

        // Cari elemen yang mengandung teks "Jadwal Pelaksanaan SNBP 2023"
        $targetElement = $crawler->filter('strong:contains("Jadwal Pelaksanaan SNBP 2023")')->each(function ($module, $index) use (&$rowData) {
            dd($module->text());
        });

        // Ambil elemen-elemen setelah elemen target
        $jadwal = $targetElement->nextAll()->each(function (Crawler $node, $i) {
            return $node->text();
        });
        dd($jadwal);
        // Tampilkan jadwal yang telah ditemukan
        return $jadwal;
    }
}
