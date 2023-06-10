<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\daftar_ulang_simandiri;
use Dialogflow\WebhookClient;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\CrawlController;

class WebhookController extends Controller
{
    public function handle(Request $requestData)
    {
        $agent = new WebhookClient(json_decode(file_get_contents('php://input'),true));
        $intent = $agent->getIntent();
        $textResult = "";

        if ($intent == '5c.TimelineSIMANDIRI') {//Ini untuk Pemilihan intent
            $data = daftar_ulang_simandiri::all(); // Ganti dengan model dan query yang sesuai
            $stringDB = "Berikut ini adalah hasil dari jadwal pendaftaran SIMANDIRI : \n";
            foreach ($data as $item) {
            $keterangan = $item->keterangan;
            $tanggalMulai = $item->tanggal_mulai;
            $tanggalSelesai = $item->tanggal_selesai == "0000-00-00" ? "" : "s.d. " . $item->tanggal_selesai;
            $stringDB .=  $keterangan .' '. $tanggalMulai .' '. $tanggalSelesai . "\n"; 
            // Bentuk pesan untuk setiap data
            }
            $textResult .= $stringDB;
        }
        else if($intent == 'NAMAINTEN'){
            // data
        }
       
        $agent->reply($textResult);
        header('Content-type: application/json');
        echo json_encode($agent->render());
    }

    private function handleUnknownIntent()
    {
        // Aksi yang akan dijalankan jika intent tidak dikenali
        $fulfillmentText = 'Intent tidak dikenali';

        // Bentuk respons dalam format yang sesuai dengan Dialogflow
        $response = [
            'fulfillmentText' => $fulfillmentText
        ];

        return response()->json($response);
    }
}

