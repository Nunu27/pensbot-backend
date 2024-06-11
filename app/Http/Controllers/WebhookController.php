<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Handlers\InformasiPendaftaranHandler;
use App\Http\Controllers\Handlers\PrestasiHandler;
use App\Http\Controllers\Handlers\TimelinePendaftaranHandler;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    protected $prestasiHandler;
    protected $informasiPendaftaranHandler;
    protected $timelinePendaftaranHandler;

    public function __construct()
    {
        $this->prestasiHandler = new PrestasiHandler();
        $this->informasiPendaftaranHandler = new InformasiPendaftaranHandler();
        $this->timelinePendaftaranHandler = new TimelinePendaftaranHandler();
    }

    public function handleWebhook(Request $request)
    {
        $payload = $request->json()->all();

        return response()->json([
            "fulfillmentMessages" => [
                [
                    "payload" => [
                        "telegram" => [
                            "text" => $this->processRequest($payload),
                            "parse_mode" => "Markdown"
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function getHandler($intent)
    {

        return match ($intent) {
            '1c.Prestasi' => [$this->prestasiHandler, 'prestasi'],

            '3b1.KuotaPendaftaran' => [$this->informasiPendaftaranHandler, 'kuotaPendaftaran'],
            '3b2.CaraDaftarUlang' => [$this->informasiPendaftaranHandler, 'daftarUlang'],
            '3b3.DaftarSNBP' => [$this->informasiPendaftaranHandler, 'SNBP'],
            '3b4.DaftarSNBT' => [$this->informasiPendaftaranHandler, 'SNBT'],
            '3b5.DaftarMandiri' => [$this->informasiPendaftaranHandler, 'SIMANDIRI'],

            '3c1.TimelinePerkuliahan' => [$this->timelinePendaftaranHandler, 'perkuliahan'],
            '3c2.TimelineSNBP' => [$this->timelinePendaftaranHandler, 'SNBP'],
            '3c3.TimelineSIMANDIRI' => [$this->timelinePendaftaranHandler, 'SIMANDIRI'],
            '3c4.TimelineSNBT' => [$this->timelinePendaftaranHandler, 'SNBT'],

            default => null,
        };
    }

    private function processRequest($payload)
    {
        $handler = $this->getHandler($payload['queryResult']['intent']['displayName']);

        if (!$handler) {
            return '
Maaf penseepisbot tidak mengerti apa yang kamu maksud😥
Untuk informasi lebih lanjut silahkan hubungi nomor berikut wa.me/6281133305005 atau kembali ke halaman utama dengan mengetik /start';
        }

        try {
            return call_user_func($handler, $payload) . "\nApakah jawaban penseepisbot sudah cukup membantu?";
        } catch (\Throwable $th) {
            error_log(($th));
            return 'Terjadi kesalahan saat memroses permintaan';
        }
    }
}
