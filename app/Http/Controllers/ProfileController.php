<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index() {
        return view('profile');
    }

    public function get_package() {
        $data = DB::table('daftar_bo')
            ->select('*', 'daftar_bo.id AS id')
            ->where(['daftar_bo.id' => session()->get('id_bo')])
            ->join('subscriptions', 'subscriptions.id', '=', 'daftar_bo.paket_subs')
            ->first();
        return response()->json($data, 200);
    }

    public function ubah_nama_bo() {
        $data = json_decode(request()->data);
        DB::table('users')->where('id', session()->get('id_bo'))->update(['name' => $data]);
        DB::table('daftar_bo')->where('id', session()->get('id_bo'))->update(['nama_bo' => $data]);
        session()->forget('nama_bo');
        session()->put('nama_bo', $data);
        return response()->json($data, 200);
    }

    public function ubah_password_bo() {
        $data = json_decode(request()->data);
        DB::table('users')->where('id', session()->get('id_bo'))->update(['password' => Hash::make($data)]);
        return response()->json($data, 200);
    }
}
