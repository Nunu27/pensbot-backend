<?php

namespace App\Http\Controllers\Scrapers;

class PMBScraper extends Scraper
{
    public function snbp()
    {
        $crawler = $this->crawl('https://pmb.pens.ac.id/index.php/snbp/');

        $tanggal = $crawler->filter('.et_pb_column_8 strong');

        $proses_jadwal = function ($e, $i) use ($tanggal) {
            $tanggal_raw = $tanggal->eq($i)->text();
            @[$mulai, $selesai] = explode(' – ', $tanggal_raw);
            $tanggal_selesai = date("Y-m-d", $this->formatter->parse($selesai ?? $mulai));

            return [
                'keterangan' => $e->text(),
                'tanggal' => $tanggal_raw,
                'tanggal_selesai' => $tanggal_selesai,
            ];
        };

        $this->save(
            'timeline_snbp',
            $crawler->filter('.et_pb_column_7 p')->each($proses_jadwal)
        );

        return response()->json([
            'success' => true,
            'message' => 'Data SNBP berhasil disimpan'
        ]);
    }

    private function parseTimelineSimandiri($crawler)
    {

        $info = $crawler->filter('.et_pb_section_1 .et_pb_text_inner p');

        $proses_info = function ($e) {
            [$keterangan, $tanggal] = explode(' : ', $e);

            return [
                'keterangan' => $this->trim($keterangan),
                'tanggal' => $this->trim($tanggal),
            ];
        };
        $proses_gelombang = function ($e, $i) use ($info, $proses_info) {
            return [
                'keterangan' => $e->text(),
                'jadwal' => array_map($proses_info, explode('<br>', $info->eq($i)->html())),
            ];
        };

        return $crawler->filter('.et_pb_section_1 .et_pb_text_inner strong')->each($proses_gelombang);
    }

    private function parseAdministrasiSimandiri($crawler)
    {
        $proses_ipi = function ($e) {
            $kategori_el = $e->nextAll()->filter('td');

            $proses_kategori = function ($kategori, $besaran) {
                return [
                    'nama' => $this->trim($kategori),
                    'besaran' => "$besaran,-",
                ];
            };

            return [
                'keterangan' => $this->trim($e->text()),
                'daftar_kategori' => array_map(
                    $proses_kategori,
                    array_slice(explode('·', $kategori_el->eq(0)->text()), 1),
                    explode(',- ', substr($kategori_el->eq(1)->text(), 0, -2))
                ),
            ];
        };
        $proses_ukt = function ($e) {
            $td = $e->filter('td');

            return [
                'kelompok' => $td->eq(0)->text(),
                'besaran' => $td->eq(1)->text(),
            ];
        };

        return [
            'pendaftaran' => $crawler->filter('.et_pb_text_16 strong')->text(),
            'ipi' => $crawler->filter('.et_pb_text_18 tbody > tr:nth-child(even)')->each($proses_ipi),
            'ukt' => $crawler->filter('.et_pb_text_19 tbody > tr:not(:first-child)')->each($proses_ukt),
        ];
    }

    public function simandiri()
    {
        $crawler = $this->crawl('https://pmb.pens.ac.id/index.php/simandiri/');

        $this->save(
            'timeline_simandiri',
            $this->parseTimelineSimandiri($crawler),
        );
        $this->save(
            'administrasi_simandiri',
            $this->parseAdministrasiSimandiri($crawler),
        );

        return response()->json([
            'success' => true,
            'message' => 'Data Simandiri berhasil disimpan',
        ]);
    }
    public function snbt()
    {
        $crawler = $this->crawl('https://pmb.pens.ac.id/index.php/snbt/');

        $tanggal = $crawler->filter('.et_pb_column_6 p');

        $proses_jadwal = function ($e, $i) use ($tanggal) {
            $tanggal_raw = $tanggal->eq($i)->text();
            @[$mulai, $selesai] = explode(' – ', $tanggal_raw);
            $tanggal_selesai = date("Y-m-d", $this->formatter->parse($selesai ?? $mulai));

            return [
                'keterangan' => $e->text(),
                'tanggal' => $tanggal_raw,
                'tanggal_selesai' => $tanggal_selesai,
            ];
        };

        $this->save(
            'timeline_snbt',
            $crawler->filter('.et_pb_column_5 p')->each($proses_jadwal)
        );

        return response()->json([
            'success' => true,
            'message' => 'Data SNBT berhasil disimpan'
        ]);
    }
}