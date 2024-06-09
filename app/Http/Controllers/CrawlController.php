<?php

namespace App\Http\Controllers;

use App\Models\CrawlData;
use GuzzleHttp\Client;
use IntlDateFormatter;
use Symfony\Component\DomCrawler\Crawler;

class CrawlController extends Controller
{
    private $client;
    private $formatter;

    public function __construct()
    {
        $this->client = new Client();
        $this->formatter = new IntlDateFormatter(
            'id_ID',
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE
        );
        $this->formatter->setPattern('d MMMM yyyy');
    }

    private function getDateString($date, $callback)
    {
        @[$a_tanggal, $a_bulan, $a_tahun] = explode(' ', $date);
        @[, $b_bulan, $b_tahun] = explode(' ', $callback ?? $date);

        return "$a_tanggal " . ($a_bulan ?? $b_bulan) . ' ' . ($a_tahun ?? $b_tahun);
    }

    public function index()
    {
        return [
            'success' => true,
            'message' => 'Backend PENSBot'
        ];
    }

    public function scrapSNBP()
    {
        $response = $this->client->get('https://pmb.pens.ac.id/index.php/snbp/');
        $html = $response->getBody()->getContents();
        $crawler = new Crawler($html);

        $tanggal = $crawler->filter('.et_pb_column_8 strong');

        $proses_jadwal = function ($e, $i) use ($tanggal) {
            $tanggal_raw = $tanggal->eq($i)->text();
            @[$mulai, $selesai] = explode(' – ', $tanggal_raw);
            $tanggal_selesai = date("Y-m-d", $this->formatter->parse($selesai ? $this->getDateString($selesai, $mulai) : $mulai));

            return [
                'keterangan' => $e->text(),
                'tanggal' => $tanggal_raw,
                'tanggal_selesai' => $tanggal_selesai,
            ];
        };

        CrawlData::updateOrCreate([
            'id' => 'timeline_snbp',
            'data' => json_encode($crawler->filter('.et_pb_column_7 p')->each($proses_jadwal)),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data SNBP berhasil disimpan'
        ]);
    }

    public function scrapSimandiri()
    {
        $response = $this->client->get('https://pmb.pens.ac.id/index.php/simandiri/');
        $html = $response->getBody()->getContents();
        $crawler = new Crawler($html);

        $info = $crawler->filter('.et_pb_section_1 .et_pb_text_inner p');

        $proses_info = function ($e) {
            [$keterangan, $tanggal] = explode(' : ', $e);

            return [
                'keterangan' => trim(html_entity_decode($keterangan), " \n\r\t\v\x00\xc2\xa0"),
                'tanggal' => trim($tanggal),
            ];
        };
        $proses_gelombang = function ($e, $i) use ($info, $proses_info) {
            return [
                'keterangan' => $e->text(),
                'jadwal' => array_map($proses_info, explode('<br>', $info->eq($i)->html())),
            ];
        };

        CrawlData::updateOrCreate([
            'id' => 'timeline_simandiri',
            'data' => json_encode($crawler->filter('.et_pb_section_1 .et_pb_text_inner strong')->each($proses_gelombang)),
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Data Simandiri berhasil disimpan',
        ]);
    }
    public function scrapSNBT()
    {
        $response = $this->client->get('https://pmb.pens.ac.id/index.php/snbt/');
        $html = $response->getBody()->getContents();
        $crawler = new Crawler($html);

        $tanggal = $crawler->filter('.et_pb_column_6 p');

        $proses_jadwal = function ($e, $i) use ($tanggal) {
            $tanggal_raw = $tanggal->eq($i)->text();
            @[$mulai, $selesai] = explode(' – ', $tanggal_raw);
            $tanggal_selesai = date("Y-m-d", $this->formatter->parse($selesai ? $this->getDateString($selesai, $mulai) : $mulai));

            return [
                'keterangan' => $e->text(),
                'tanggal' => $tanggal_raw,
                'tanggal_selesai' => $tanggal_selesai,
            ];
        };

        CrawlData::updateOrCreate([
            'id' => 'timeline_snbt',
            'data' => json_encode($crawler->filter('.et_pb_column_5 p')->each($proses_jadwal)),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data SNBT berhasil disimpan'
        ]);
    }
}
