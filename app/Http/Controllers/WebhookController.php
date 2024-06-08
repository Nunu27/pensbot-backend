<?php
namespace App\Http\Controllers;

use App\Models\daftar_ulang_simandiri;
use App\Models\daftar_ulang_snbp;
use App\Models\daftar_ulang_snbt;
use Dialogflow\WebhookClient;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function handle(Request $requestData)
    {
        $agent = new WebhookClient(json_decode(file_get_contents('php://input'), true));
        $intent = $agent->getIntent();
        $textResult = "";

        if ($intent == '5c.TimelineSIMANDIRI') { //Ini untuk Pemilihan intent
            $data = daftar_ulang_simandiri::all(); // Ganti dengan model dan query yang sesuai
            $stringDB = "Berikut ini adalah hasil dari jadwal pendaftaran SIMANDIRI : \n";
            foreach ($data as $item) {
                $keterangan = $item->keterangan;
                $tanggalMulai = $item->tanggal_mulai;
                $tanggalSelesai = $item->tanggal_selesai == "0000-00-00" || $item->tanggal_selesai == null ? "" : "s.d. " . $item->tanggal_selesai;
                $stringDB .= $keterangan . ' ' . $tanggalMulai . ' ' . $tanggalSelesai . "\n";
                // Bentuk pesan untuk setiap data
            }
            $textResult .= $stringDB . "\nApakah jawaban PENSbot sudah cukup membantu?";
        } else if ($intent == '5b.TimelineSNBP') {
            $data = daftar_ulang_snbp::all(); // Ganti dengan model dan query yang sesuai
            $stringDB = "Berikut ini adalah hasil dari jadwal pendaftaran SNBP : \n";
            foreach ($data as $item) {
                $keterangan = $item->keterangan;
                $tanggalMulai = $item->tanggal_mulai;
                $tanggalSelesai = $item->tanggal_selesai == "0000-00-00" || $item->tanggal_selesai == null ? "" : "s.d. " . $item->tanggal_selesai;
                $stringDB .= $keterangan . ' ' . $tanggalMulai . ' ' . $tanggalSelesai . "\n";
                // Bentuk pesan untuk setiap data
            }
            $textResult .= $stringDB . "\nApakah jawaban PENSbot sudah cukup membantu?";
        } else if ($intent == '5d.TimelineSNBT') {
            $data = daftar_ulang_snbt::all(); // Ganti dengan model dan query yang sesuai
            $stringDB = "Berikut ini adalah hasil dari jadwal pendaftaran SNBT : \n";
            foreach ($data as $item) {
                $keterangan = $item->keterangan;
                $tanggalMulai = $item->tanggal_mulai;
                $tanggalSelesai = $item->tanggal_selesai == "0000-00-00" || $item->tanggal_selesai == null ? "" : "s.d. " . $item->tanggal_selesai;
                $stringDB .= $keterangan . ' ' . $tanggalMulai . ' ' . $tanggalSelesai . "\n";
                // Bentuk pesan untuk setiap data
            }
            $textResult .= $stringDB . "\nApakah jawaban PENSbot sudah cukup membantu?";
        }

        $agent->reply($textResult);
        header('Content-type: application/json');
        echo json_encode($agent->render());
    }

    public function handleWebhook(Request $request)
    {
        $data = $request->json()->all();

        return response()->json([
            'fulfillmentText' => $this->processRequest($data)
        ]);
    }

    private function processRequest($data)
    {
        $intent = $data['queryResult']['intent']['displayName'];

        try {
            switch ($intent) {
                case '3c2.TimelineSNBP':
                    return $this->handleSNBP($data);
                case '3c3.TimelineSIMANDIRI':
                    return $this->handleSimandiri($data);
                case '5c.TimelineSIMANDIRI':
                    return $this->handleYourIntent($data);
                default:
                    return 'This is the default response';
            }
        } catch (\Throwable $th) {
            return 'Terjadi kesalahan saat memroses permintaan';
        }
    }


    private function handleSNBP($data)
    {
        $snbp = daftar_ulang_snbp::all(); // Ganti dengan model dan query yang sesuai
        $stringDB = "Berikut ini adalah hasil dari jadwal pendaftaran SNBP : \n";
        foreach ($snbp as $item) {
            $keterangan = $item->keterangan;
            $tanggalMulai = $item->tanggal_mulai;
            $tanggalSelesai = $item->tanggal_selesai == "0000-00-00" || $item->tanggal_selesai == null ? "" : "s.d. " . $item->tanggal_selesai;
            $stringDB .= $keterangan . ' ' . $tanggalMulai . ' ' . $tanggalSelesai . "\n";
            // Bentuk pesan untuk setiap data
        }
        return $stringDB . "\nApakah jawaban PENSbot sudah cukup membantu?";
    }
    private function handleSimandiri($data)
    {
        $simandiri = daftar_ulang_simandiri::all(); // Ganti dengan model dan query yang sesuai
        $stringDB = "Berikut ini adalah hasil dari jadwal pendaftaran SIMANDIRI : \n";
        foreach ($simandiri as $item) {
            $keterangan = $item->keterangan;
            $tanggalMulai = $item->tanggal_mulai;
            $tanggalSelesai = $item->tanggal_selesai == "0000-00-00" || $item->tanggal_selesai == null ? "" : "s.d. " . $item->tanggal_selesai;
            $stringDB .= $keterangan . ' ' . $tanggalMulai . ' ' . $tanggalSelesai . "\n";
        }

        return $stringDB . "\nApakah jawaban PENSbot sudah cukup membantu?";
    }

}
