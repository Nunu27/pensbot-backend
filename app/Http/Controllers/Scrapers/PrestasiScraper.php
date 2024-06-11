<?php

namespace App\Http\Controllers\Scrapers;

class PrestasiScraper extends Scraper
{
    private function scrapeDataTable($id)
    {
        $crawler = $this->crawl("https://kemahasiswaan.pens.ac.id/$id");

        $proses_entri = function ($e, $i) {
            $data = $e->siblings();

            return [
                'nama' => $e->text(),
                'keterangan' => $data->eq(2)->text(),
                'tanggal' => $data->eq(3)->text(),
                'penyelenggara' => $data->eq(4)->text(),
                'tingkat' => $data->eq(5)->text(),
                'tahun' => $data->eq(6)->text(),
            ];
        };

        return $crawler->filter("tbody .column-1:not(:empty)")->slice(0, 10)->each($proses_entri);
    }

    public function prestasi()
    {
        $this->save('prestasi', [
            [
                'keterangan' => 'Penalaran',
                'data' => $this->scrapeDataTable('prestasi-penalaran'),
            ],
            [
                'keterangan' => 'Minat bakat',
                'data' => $this->scrapeDataTable('minat-bakat'),
            ],
        ]);

        return response()->json([
            'success' => true,
            'data' => "Data prestasi berhasil disimpan"
        ]);
    }
}