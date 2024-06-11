<?php

namespace App\Http\Controllers\Handlers;

class PrestasiHandler extends Handler
{
    public function prestasi($payload)
    {
        $data = $this->getData('prestasi');
        $stringDB = "Prestasi PENS Terkini :\n";

        foreach ($data as $kategori) {
            $stringDB .= "\n*$kategori->keterangan*:\n";

            foreach ($kategori->data as $index => $item) {
                $stringDB .= $index + 1 . ". $item->nama - $item->keterangan di $item->penyelenggara pada tanggal $item->tanggal. Tahun $item->tahun\n";
            }
        }

        return $stringDB;
    }
}
