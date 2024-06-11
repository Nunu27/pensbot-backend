<?php

namespace App\Http\Controllers\Scrapers;

use App\Http\Controllers\Controller;
use App\Models\CrawlData;
use GuzzleHttp\Client;
use IntlDateFormatter;
use Symfony\Component\DomCrawler\Crawler;

class Scraper extends Controller
{
    protected $client;
    protected $formatter;

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

    protected function getDateString($date, $callback)
    {
        @[$a_tanggal, $a_bulan, $a_tahun] = explode(' ', $date);
        @[, $b_bulan, $b_tahun] = explode(' ', $callback ?? $date);

        return "$a_tanggal " . ($a_bulan ?? $b_bulan) . ' ' . ($a_tahun ?? $b_tahun);
    }

    protected function crawl($url)
    {
        $response = $this->client->get($url);
        $html = $response->getBody()->getContents();
        return new Crawler($html);
    }

    protected function save($id, $data)
    {
        CrawlData::updateOrCreate([
            'id' => $id,
            'data' => json_encode($data)
        ]);
    }

    protected function trim($str)
    {
        return trim(html_entity_decode($str), " \n\r\t\v\x00\xc2\xa0");
    }
}
