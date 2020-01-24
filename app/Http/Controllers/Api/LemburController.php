<?php

namespace App\Http\Controllers\Api;

use App\Absensi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Lembur;

class LemburController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 200, 'message' => 'Sukses', 'data' =>
            [
                [
                    'user_id' => 1,
                    'absensi_id' => 2,
                    'lembur_awal' => '17:00:00',
                    'lembur_akhir' => '12:00:00',
                    'konsumsi' => 100000,
                    'keterangan' => 'lembur',
                    'foto' => 'lembur.jpg',
                    'status' => 'Menunggu'
                ],
                [
                    'user_id' => 2,
                    'absensi_id' => 3,
                    'lembur_awal' => '17:00:00',
                    'lembur_akhir' => '11:00:00',
                    'konsumsi' => 120000,
                    'keterangan' => 'lembur',
                    'foto' => 'lembur.jpg',
                    'status' => 'Menunggu'
                ],
                [
                    'user_id' => 3,
                    'absensi_id' => 5,
                    'lembur_awal' => '18:00:00',
                    'lembur_akhir' => '11:00:00',
                    'konsumsi' => 190000,
                    'keterangan' => 'lembur',
                    'foto' => 'lembur.jpg',
                    'status' => 'Disetujui'
                ],
                [
                    'user_id' => 4,
                    'absensi_id' => 8,
                    'lembur_awal' => '15:00:00',
                    'lembur_akhir' => '04:00:00',
                    'konsumsi' => 120000,
                    'foto' => 'lembur.jpg',
                    'status' => 'Ditolak'
                ]
            ]
        ]);
    }

    public function create()
    {
        $carbon = new Carbon();
        $lembur = new Lembur();
        $check_absensi_today = Absensi::where('user_id', '=', Auth::user()->id)->where('tanggal', '=', $carbon->toDateString())->first();

        $check_lembur = Lembur::where('user_id', '=', Auth::user()->id)->where('absensi_id', '=', $check_absensi_today['id'])->first();

        if ($check_absensi_today === null) {
            return response()->json(['status' => 400, 'message' => 'Anda belum absensi hari ini!']);
        }

        if ($check_lembur !== null) {
            return response()->json(['status' => 400, 'message' => 'Anda sudah mengajukan lembur hari ini!']);
        }

        $lembur->user_id = Auth::user()->id;
        $lembur->absensi_id = $check_absensi_today['id'];
        $lembur->tanggal = $carbon->toDateString();
        $lembur->lembur_awal = $carbon->toTimeString();
        $lembur->lembur_akhir = $carbon->toTimeString();
        $lembur->konsumsi = 50000;
        $lembur->keterangan = 'Lembur';
        $lembur->foto = 'lembur.jpg';
        $lembur->status = 'Menunggu';
        $lembur->save();

        return response()->json(['status' => 200, 'message' => 'Berhasil lembur!. Mohon tunggu admin untuk mempersetujuinya.']);
    }
}
