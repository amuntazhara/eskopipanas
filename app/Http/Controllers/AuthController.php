<?php

namespace App\Http\Controllers;

use stdClass;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function index() {
        return view("login");
    }

    public function validateLogin() {
        $data = json_decode(request()->data);

        if ($data->email == null) {
            return response()->json('Email harus diisi.', 400);
        } else if ($data->password == null) {
            return response()->json('Password harus diisi.', 400);
        } else {
            $check = Auth::attempt(['email' => $data->email, 'password' => $data->password]);
            if($check) {
                $check_admin = $this->check_admin($data->email);
                if($check_admin == false) {
                    $bo = DB::table('daftar_bo')
                        ->join('users', 'users.nama_bo', '=', 'daftar_bo.id')
                        ->join('subscriptions', 'daftar_bo.paket_subs', '=', 'subscriptions.id')
                        ->select('daftar_bo.id AS id_bo', 'daftar_bo.nama_bo AS nama_bo', 'subscribe', 'id_telegram', 'telegram')
                        ->where('email', $data->email)
                        ->first();
                    session()->put('nama_bo', $bo->nama_bo);
                    session()->put('id_bo', $bo->id_bo);
                    session()->put('subscription', $bo->subscribe);
                    session()->put('id_telegram', $bo->id_telegram);
                    session()->put('telegram', $bo->telegram);
                    session()->put('user', $data->email);
                    return response()->json(0, 200);
                } else {
                    session()->put('user', $data->email);
                    return response()->json(1, 200);
                }
            } else {
                return response()->json('Email dan Password tidak sesuai.', 400);
            }
        }
    }

    public function logout() {
        Auth::logout();
        return redirect('login');
    }

    private function check_admin($email) {
        $role = DB::table('users')->select('nama_bo')->where('email', $email)->first();
        if($role->nama_bo == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function get_session() {
        $data = new stdClass;
        $data->subscription = null;
        $data->id_bo = session()->get('id_bo');
        $data->nama_bo = session()->get('nama_bo');
        $data->id_telegram = session()->get('id_telegram');
        $data->telegram = session()->get('telegram');
        if(session()->has('subscription'))
            $data->subscription = session()->get('subscription');

        return response()->json($data, 200);
    }

    public function cek_password_bo() {
        $data = json_decode(request()->data);
        $user = User::find(session()->get('id_bo'));
        if (Hash::check($data, $user->password))
            return response()->json(true, 200);
        else
            return response()->json(false, 400);
    }
}
