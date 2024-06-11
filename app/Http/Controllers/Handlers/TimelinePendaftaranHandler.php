<?php

namespace App\Http\Controllers\Handlers;

class TimelinePendaftaranHandler extends Handler
{
    public function SNBP($payload)
    {
        $data = $this->getData('timeline_snbp');
        $stringDB = "Berikut jadwal pendaftaran *SNBP* : \n\n";

        foreach ($data as $item) {
            $stringDB .= "*$item->keterangan* | $item->tanggal\n";
        }

        return $stringDB . "\n\* _Seluruh kegiatan pada hari yang sudah ditentukan akan diakhiri pada pukul 15.00 WIB_\n";
    }
    public function SIMANDIRI($payload)
    {
        $data = $this->getData('timeline_simandiri');
        $stringDB = "Berikut jadwal pendaftaran *SIMANDIRI* : \n\n";

        foreach ($data as $gelombang) {
            $stringDB .= "*$gelombang->keterangan*\n";
            foreach ($gelombang->jadwal as $jadwal) {
                $stringDB .= "- $jadwal->keterangan | $jadwal->tanggal\n";
            }
            $stringDB .= "\n";
        }

        return $stringDB;
    }
    public function SNBT($payload)
    {
        $data = $this->getData('timeline_snbt');
        $stringDB = "Berikut jadwal pendaftaran *SNBT* : \n\n";

        foreach ($data as $item) {
            $stringDB .= "*$item->keterangan* | $item->tanggal\n";
        }

        return $stringDB . "\n\* _Seluruh kegiatan pada hari yang sudah ditentukan akan diakhiri pada pukul 15.00 WIB_\n";
    }
}
