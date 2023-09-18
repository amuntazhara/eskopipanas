<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    function index() {
        return view('home');
    }

    public function fetch_data() {
        $domain = request()->domain;
        // $url = "https://trustpositif.kominfo.go.id/Rest_server/getrecordsname_home";
        $url = "https://indiwtf.upset.dev/api/check?domain=$domain";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        curl_close($curl);

        return json_decode($result);
    }

    public function get_websites() {
        $get = DB::table('websites')
            ->join('daftar_bo', 'websites.nama_bo', '=', 'daftar_bo.id')
            ->select('websites.id AS id', 'domain', 'daftar_bo.nama_bo AS nama_bo', 'kontak')
            ->get();
        return $get;
    }

    public function bot_check() {
        $token = "6303246029:AAFLpHSCUa8aR7CWugzjMfAImG4P_AIpwK0";
        $method = 'getMe';
        $api_url = "http://api.telegram.org/bot{$token}/{$method}";
        $res = file_get_contents($api_url);
        return response()->json($res, 200);
    }

    public function add_domain() {
        $data = json_decode(request()->data);
        $check_paket = DB::table('daftar_bo')->select('paket_subs', 'subscribe')->where('id', $data->namaBO)->first();
        $detail_sub = DB::table('subscriptions')->select('limit_domain', 'durasi')->where('id', $check_paket->paket_subs)->first();
        $total_domain = DB::table('websites')->where('nama_bo', $data->namaBO)->count();

        // Get selisih hari dengan tanggal akhir subscribe
        $bo_subs_start = date('Y-m-d', strtotime($check_paket->subscribe));
        $bo_subs_end = strtotime($this->add($bo_subs_start, $detail_sub->durasi));
        $today = strtotime(date('Y-m-d'));
        $remaining_days = ($bo_subs_end - $today) / 86400;

        if (($total_domain + 1) > $detail_sub->limit_domain) {
            return response()->json('limit', 400);
        } else if ($remaining_days < 0) {
            return response()->json('endsub', 400);
        } else {
            DB::table('websites')->insert(['domain' => $data->domain, 'nama_bo' => $data->namaBO]);
            return response()->json('ok', 200);
        }
    }

    public function ubah_domain() {
        $data = json_decode(request()->data);
        $check_paket = DB::table('daftar_bo')->select('paket_subs', 'subscribe')->where('id', $data->namaBO)->first();
        $detail_sub = DB::table('subscriptions')->select('durasi')->where('id', $check_paket->paket_subs)->first();

        // Get selisih hari dengan tanggal akhir subscribe
        $bo_subs_start = date('Y-m-d', strtotime($check_paket->subscribe));
        $bo_subs_end = strtotime($this->add($bo_subs_start, $detail_sub->durasi));
        $today = strtotime(date('Y-m-d'));
        $remaining_days = ($bo_subs_end - $today) / 86400;

        if ($remaining_days < 0) {
            return response()->json('endsub', 400);
        } else {
            DB::table('websites')->where('id', $data->idDomain)->update(['domain' => $data->domain]);
            return response()->json('ok', 200);
        }
    }

    public function delete_domain() {
        $data = json_decode(request()->data);
        $delete = DB::table('websites')->where('id', $data->id)->delete();
        if($delete)
            return response()->json('ok', 200);
        else
            return response()->json('ok', 400);
    }

    private function add($date_str, $months)
    {
        $date = new DateTime($date_str);

        // We extract the day of the month as $start_day
        $start_day = $date->format('j');

        // We add 1 month to the given date
        $date->modify("+{$months} month");

        // We extract the day of the month again so we can compare
        $end_day = $date->format('j');

        if ($start_day != $end_day)
        {
            // The day of the month isn't the same anymore, so we correct the date
            $date->modify('last day of last month');
        }

        $date = $date->format('Y-m-d');

        return $date;
    }
}
