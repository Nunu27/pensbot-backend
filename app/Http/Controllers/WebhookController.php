<?php
namespace App\Http\Controllers;

use App\Models\CrawlData;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    private function getData($id)
    {

        $item = CrawlData::find($id);
        return json_decode($item->data);
    }

    public function handleWebhook(Request $request)
    {
        $data = $request->json()->all();

        return response()->json([
            "fulfillmentMessages" => [
                [
                    "payload" => [
                        "telegram" => [
                            "text" => $this->processRequest($data),
                            "parse_mode" => "Markdown"
                        ]
                    ]
                ]
            ]
        ]);
    }

    private function processRequest($data)
    {
        $intent = $data['queryResult']['intent']['displayName'];

        try {
            return match ($intent) {
                '3c2.TimelineSNBP' => $this->handleSNBP($data),
                '3c3.TimelineSIMANDIRI' => $this->handleSimandiri($data),
                '3c4.TimelineSNBT' => $this->handleSNBT($data),
                default => 'Maaf penseepisbot tidak mengerti apa yang kamu maksud😥\nUntuk informasi lebih lanjut silahkan hubungi nomor berikut wa.me/6281133305005 atau kembali ke halaman utama dengan mengetik /start',
            };
        } catch (\Throwable $th) {
            return 'Terjadi kesalahan saat memroses permintaan';
        }
    }


    private function handleSNBP($data)
    {
        $data = $this->getData('timeline_snbp');
        $stringDB = "Berikut jadwal pendaftaran SNBP : \n";

        foreach ($data as $item) {
            $stringDB .= '*' . $item->keterangan . '* | ' . $item->tanggal . "\n";
        }

        return $stringDB . "\n\nApakah jawaban PENSbot sudah cukup membantu?";
    }
    private function handleSimandiri($data)
    {
        $data = $this->getData('timeline_simandiri');
        $stringDB = "Berikut jadwal pendaftaran SIMANDIRI : \n";

        foreach ($data as $item) {
            $stringDB .= '*' . $item->keterangan . '* | ' . $item->tanggal . "\n";
        }

        return $stringDB . "\n\nApakah jawaban PENSbot sudah cukup membantu?";
    }

}
