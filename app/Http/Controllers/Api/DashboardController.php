<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\User;
use App\Role;
use App\Absensi;
use App\Izin;
use Carbon\Carbon;
use App\Lembur;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $total_pegawai = User::get()->filter(function ($user) {
            return $user->roles()->first()['id'] !== 1;
        })->values()->count();
        $total_pegawai_absen = Absensi::where('tanggal', Carbon::now()->toDateString())->get()->sortBy('absensi_masuk')->take(5)->count();

        $sudah_absensi = [];
        foreach (Absensi::where('tanggal', Carbon::now()->toDateString())->get()->sortBy('absensi_masuk')->take(5) as $absen) {
            $sudah_absensi[] = [
                'name' => $absen->user->name,
                'absensi_masuk' => $absen->absensi_masuk,
                'absensi_keluar' => $absen->absensi_keluar,
                'id' => $absen->id
            ];
        }

        $belum_absensi = [];
        foreach (User::all() as $user) {
            if ( $user->roles()->first()['id'] !== 1 ) {
                if (!Absensi::find($user->id)) {
                    $belum_absensi[] = ['name' => User::find($user->id)->name, 'id' => $user->id];
                }
            }
        }

        $izin = Izin::join('users', 'users.id', '=', 'izins.user_id')
        ->where(DB::raw('UNIX_TIMESTAMP(tanggal_mulai)'), '<=', Carbon::now()->unix())
        ->where(DB::raw('(UNIX_TIMESTAMP(tanggal_selesai) + 60 * 60 * 24)'), '>=', Carbon::now()->unix());

        return response()->json([
            'status' => 200,
            'data' => [
                'total_pegawai' => $total_pegawai,
                'total_pegawai_absen' => $total_pegawai_absen,
                'total_pegawai_izin' => $izin->count(),
                'total_pegawai_lembur' => Lembur::where('tanggal', Carbon::now()->toDateString())->count(),
                'pegawai_sudah_absen' => $sudah_absensi,
                'pegawai_izin' => collect($izin->get())->take(5)
            ]
        ]);
    }
}
