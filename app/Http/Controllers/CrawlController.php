<?php

namespace App\Http\Controllers;

use App\Models\daftar_ulang_simandiri;
use App\Models\daftar_ulang_snbp;
use App\Models\daftar_ulang_snbt;
use DOMXPath;
use GuzzleHttp\Client;
use IntlDateFormatter;
use Symfony\Component\DomCrawler\Crawler;
use tidy;

class CrawlController extends Controller
{
    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function index()
    {
        $response = $this->client->get('https://pmb.pens.ac.id/index.php/simandiri/');

        $html = $response->getBody()->getContents();

        $crawler = new Crawler($html);

        $data = [];

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

        // dd($result);

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

    public function scrapSNBP()
    {
        $response = $this->client->get('https://pmb.pens.ac.id/index.php/snbp/');
        $html = $response->getBody()->getContents();
        $crawler = new Crawler($html);

        $formatter = new IntlDateFormatter(
            'id_ID',
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE
        );
        $formatter->setPattern('d MMMM yyyy');

        daftar_ulang_snbp::truncate();
        $proses_jadwal = function ($e, $i) use ($crawler, $formatter) {
            $tanggal_raw = $crawler->filter('.et_pb_column_8 strong')->eq($i)->text();
            @[$mulai, $selesai] = explode(' – ', $tanggal_raw);
            @[$m_tanggal, $m_bulan, $m_tahun] = explode(' ', $mulai);
            @[$s_tanggal, $s_bulan, $s_tahun] = explode(' ', $selesai ?? $mulai);

            $tanggal_mulai = date("Y-m-d H:i:s", $formatter->parse("$m_tanggal " . ($m_bulan ?? $s_bulan) . ' ' . ($m_tahun ?? $s_tahun)));
            $tanggal_selesai = $selesai ? date("Y-m-d H:i:s", $formatter->parse("$s_tanggal " . ($s_bulan ?? $m_bulan) . ' ' . ($s_tahun ?? $m_tahun))) : $tanggal_mulai;

            daftar_ulang_snbp::create([
                'keterangan' => $e->text(),
                'tanggal_mulai' => $tanggal_mulai,
                'tanggal_selesai' => $tanggal_selesai,
            ]);
        };

        $crawler->filter('.et_pb_column_7 p')->each($proses_jadwal);
        return response()->json([
            'success' => true,
            'message' => 'Data SNBP berhasil disimpan'
        ]);
    }

    public function scrapSimandiri()
    {
        //getcontent from url
        $client = new Client();
        $response = $client->get('https://pmb.pens.ac.id/index.php/simandiri/');
        $html = $response->getBody()->getContents();
        // dd($html);
        $cleaned_html = $this->tidy_html($html);

        libxml_use_internal_errors(true);
        //*[@id="et-boc"]/div/div[3]/div[1]/div[2]/div[1]/div/p/strong
        //*[@id="et-boc"]/div/div[3]/div[1]/div[2]/div[2]/div/p/strong
        $this->domdoc->loadHTML($cleaned_html);
        $xpath = new DOMXPath($this->domdoc);

        $results = [];

        //*[@id="et-boc"]/div/div[3]/div[2]/div[2]/div[2]/div/h4
        //*[@id="et-boc"]/div/div[3]/div[2]/div[2]
        //*[@id="et-boc"]/div/div[3]/div[2]/div[2]/div[1]
        //*[@id="et-boc"]/div/div[3]/div[2]/div[2]/div[2]
        //*[@id="et-boc"]/div/div[3]/div[2]/div[2]/div[2]/div/h4
        $keterangan = $xpath->query("//*[@id=\"et-boc\"]/div/div[3]/div[2]/div[1]/div/div[.//h4|p]");
        $node_counts_keterangan = $keterangan->length;
        $jadwal = $xpath->query("//*[@id=\"et-boc\"]/div/div[3]/div[2]/div[2]/div/div[.//h4|p]");
        $node_counts_data = $jadwal->length;
        // dd($keterangan);
        $keterangan_result = [];
        if ($node_counts_data == $node_counts_keterangan) {
            foreach ($keterangan as $key => $elements) {
                $status_scrap_keterangan = preg_replace('/^\s+|\s+$|\n/', '', $elements->nodeValue);
                // dd($status_scrap_keterangan);
                $keterangan_result[$key] = [
                    'keterangan' => $status_scrap_keterangan,
                ];
            }

            // dd($keterangan);


            $schedules = [];
            // ambil data jadwal
            foreach ($jadwal as $key => $elements) {
                $status_scrap = preg_replace('/^\s+|\s+$|\n/', '', $elements->nodeValue);
                $schedules[] = $status_scrap;
            }

            foreach ($schedules as $key => $item) {
                $start_date = null;
                $end_date = null;

                // Match start and end date pattern
                // preg_match('/(\d{1,2}\s\p{L}+)\s?–\s?(\d{1,2}\s\p{L}+\s\d{4})?/u', $item, $matches);
                $item = explode('–', $item);
                if (count($item) > 1) {
                    // ambil tahun dari $item[1]
                    $dateStart = explode(' ', trim($item[0]));
                    $dateEnd = explode(' ', trim($item[1]));

                    if (count($dateStart) < 4 && count($dateStart) > 1) {
                        // dd($item);

                        // dd($dateStart);
                        if ($dateStart[1] != "") {

                            $month = $this->month(strtolower($dateStart[1]));
                        }

                        // dd($month, count($dateStart));
                        if (count($dateStart) < 3) {

                            $start_date = trim($dateStart[0]) . ' ' . trim($dateEnd[1]) . ' ' . trim($dateEnd[2]);
                            // dd($start_date, $dateEnd);
                        } else {
                            $start_date = trim($dateStart[0]) . ' ' . $month . ' ' . trim($dateEnd[3]);

                        }
                    } else {
                        // dd($dateStart);
                        $month = $this->month(strtolower($dateEnd[1]));
                        $start_date = trim($dateStart[0]) . ' ' . $month . ' ' . trim($dateEnd[2]);
                        // $start_date = $item[0];
                    }

                    // dd($dateEnd[1]);
                    $monthEnd = $this->month(strtolower($dateEnd[1]));
                    $end_date = $dateEnd[0] . ' ' . $monthEnd . ' ' . $dateEnd[2];
                } else {
                    $start_date = trim($item[0]);
                    $end_date = null;
                }
                // dd($schedules, $item);

                // if (!empty($matches)) {
                //     $start_date = trim($matches[1]);
                //     $end_date = trim($matches[2] ?? $matches[1]);
                // } else {
                //     dd($schedules, $item);
                // }
                // $results[] = [
                //     'keterangan' => $keterangan_result[$key]['keterangan'],
                //     'start_date' => $start_date,
                //     'end_date' => $end_date,
                // ];
                $results[] = [
                    'keterangan' => $keterangan_result[$key]['keterangan'],
                    'start_date' => $start_date != null ? date("Y-m-d", strtotime($start_date)) : null,
                    'end_date' => $end_date != null ? date("Y-m-d", strtotime($end_date)) : null,
                ];
            }
        }
        // dd($results);
        try {
            //delete all data
            daftar_ulang_simandiri::truncate();
            foreach ($results as $key => $val) {
                daftar_ulang_simandiri::create(
                    [
                        'keterangan' => $val['keterangan'],
                        'tanggal_mulai' => $val["start_date"] ?? null,
                        'tanggal_selesai' => $val["end_date"] ?? null,
                    ]
                );
            }
            return response()->json("Crawling Simandiri Berhasil");
        } catch (\Exception $e) {
            return response()->json($e);
        }
    }


    public function scrapSNBT()
    {
        //getcontent from url
        $client = new Client();
        $response = $client->get('https://pmb.pens.ac.id/index.php/snbt/');
        $html = $response->getBody()->getContents();
        $cleaned_html = $this->tidy_html($html);
        // dd($cleaned_html);
        libxml_use_internal_errors(true);
        //*[@id="et-boc"]/div/div[3]/div[1]/div[2]/div[1]/div/p/strong
        //*[@id="et-boc"]/div/div[3]/div[1]/div[2]/div[2]/div/p/strong
        $this->domdoc->loadHTML($cleaned_html);
        $xpath = new DOMXPath($this->domdoc);

        $results = [];

        //*[@id="et-boc"]/div/div[4]/div/div[2]
        //*[@id="et-boc"]/div/div[4]/div/div[2]/div[1]
        //*[@id="et-boc"]/div/div[4]/div/div[2]/div[2]
        //*[@id="et-boc"]/div/div[4]/div/div[2]/div[2]/div/p


        //*[@id="et-boc"]/div/div[4]/div/div[1]/div[1]/div/p

        $keterangan = $xpath->query("//*[@id=\"et-boc\"]/div/div[4]/div/div[1]/div[.//p]");
        // dd($keterangan->length);
        $node_counts_keterangan = $keterangan->length;
        $jadwal = $xpath->query("//*[@id=\"et-boc\"]/div/div[4]/div/div[2]/div[.//p]");
        $node_counts_data = $jadwal->length;
        // dd($keterangan);
        $keterangan_result = [];
        if ($node_counts_data == $node_counts_keterangan) {
            foreach ($keterangan as $key => $elements) {
                $status_scrap_keterangan = preg_replace('/^\s+|\s+$|\n/', '', $elements->nodeValue);
                // dd($status_scrap_keterangan);
                $keterangan_result[$key] = [
                    'keterangan' => $status_scrap_keterangan,
                ];
            }

            // dd($keterangan);


            $schedules = [];
            // ambil data jadwal
            foreach ($jadwal as $key => $elements) {
                $status_scrap = preg_replace('/^\s+|\s+$|\n/', '', $elements->nodeValue);
                $schedules[] = $status_scrap;
            }

            foreach ($schedules as $key => $item) {
                $start_date = null;
                $end_date = null;

                // Match start and end date pattern
                // preg_match('/(\d{1,2}\s\p{L}+)\s?–\s?(\d{1,2}\s\p{L}+\s\d{4})?/u', $item, $matches);
                $item = explode('–', $item);

                if (count($item) > 1) {
                    // ambil tahun dari $item[1]
                    $dateStart = explode(' ', $item[0]);

                    // dd($dateStart);
                    if (count($dateStart) < 4) {

                        // dd($item);

                        $dateEnd = explode(' ', $item[1]);
                        if ($dateStart[1] != "") {
                            $month = $this->month(strtolower($dateStart[1]));
                        }

                        // dd($month, count($dateStart));
                        if (count($dateStart) < 3) {
                            $month = $this->month(strtolower($dateEnd[2]));
                            $start_date = trim($item[0]) . ' ' . $month . ' ' . trim($dateEnd[3]);
                            // if ($key == 6)
                            //     dd($start_date);
                        } else {

                            $start_date = trim($dateStart[0]) . ' ' . $month . ' ' . trim($dateEnd[3]);
                        }
                    } else {
                        $month = $this->month(strtolower($dateStart[1]));

                        $start_date = trim($dateStart[0]) . ' ' . $month . ' ' . trim($dateStart[2]);
                        // dd($start_date);
                        // $start_date = $item[0];
                    }

                    // dd($item[1]);
                    // dd($dateEnd);

                    // if ($dateEnd[1] != "" && ((int) $dateEnd[1]) < 0) {

                    //     $monthEnd = $this->month(strtolower($dateEnd[1]));
                    //     $end_date = $dateEnd[0] . ' ' . $monthEnd . ' ' . $dateEnd[3];
                    // } else {

                    //     $end_date = trim($item[1]);
                    // }
                    $monthEnd = $this->month(strtolower($dateEnd[2]));
                    $end_date = $dateEnd[1] . ' ' . $monthEnd . ' ' . $dateEnd[3];
                } else {
                    $dateStart = explode(' ', $item[0]);
                    // dd($dateStart);
                    $monthEnd = $this->month(strtolower($dateStart[1]));
                    $start_date = $dateStart[0] . ' ' . $monthEnd . ' ' . $dateStart[2];
                    // $start_date = trim($item[0]);
                    // dd($dateStart, $start_date);
                    $end_date = null;
                }
                // dd($schedules, $item);

                // if (!empty($matches)) {
                //     $start_date = trim($matches[1]);
                //     $end_date = trim($matches[2] ?? $matches[1]);
                // } else {
                //     dd($schedules, $item);
                // }

                $results[] = [
                    'keterangan' => $keterangan_result[$key]['keterangan'],
                    'start_date' => $start_date != null ? date("Y-m-d", strtotime($start_date)) : null,
                    'end_date' => $end_date != null ? date("Y-m-d", strtotime($end_date)) : null,
                ];
                // $results[] = [
                //     'keterangan' => $keterangan_result[$key]['keterangan'],
                //     'start_date' => $start_date,
                //     'end_date' => $end_date,
                // ];
            }
        }
        // dd($results);
        try {
            //delete all data
            daftar_ulang_snbt::truncate();
            foreach ($results as $key => $val) {
                daftar_ulang_snbt::create(
                    [
                        'keterangan' => $val['keterangan'],
                        'tanggal_mulai' => $val["start_date"] ?? null,
                        'tanggal_selesai' => $val["end_date"] ?? null,
                    ]
                );
            }
            return response()->json("Crawling SNBT Berhasil");
        } catch (\Exception $e) {
            return response()->json($e);
        }
    }


    public function tidy_html($input_html)
    {
        $config = array('output-html' => true, 'wrap' => 800);
        // 'index' => true,

        //Detect if tidy is in configured
        if (function_exists('tidy_get_release')) {
            $tidy = new tidy();
            $tidy->parseString($input_html, $config, 'raw');
            $tidy->cleanRepair();
            $cleaned_html = tidy_get_output($tidy);
        } else {
            #Tidy not configured for this server
            $cleaned_html = $input_html;
        }

        return $cleaned_html;
    }


    public function month($month)
    {
        // cari berdasarkan key

        $data = [
            'januari' => 'january',
            'februari' => 'february',
            'maret' => 'march',
            'april' => 'april',
            'mei' => 'may',
            'juni' => 'june',
            'juli' => 'july',
            'agustus' => 'august',
            'september' => 'september',
            'oktober' => 'october',
            'november' => 'november',
            'desember' => 'december',
        ];
        return $data[$month];
    }
}
