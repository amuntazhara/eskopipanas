<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Telegram;
use App\Http\Controllers\Pagerule;
use DateTime;
use Illuminate\Support\Facades\DB;
use stdClass;

class HomeController extends Controller
{
    function index() {
        return view('home');
    }

    public function fetch_data() {
        $token = "Zzp4qIaF2g1qZKgiA7lyIMTTPP4Xn3KD";
        $data = json_decode(request()->data);
        $data = $data->domain;
        $result = "";
        // // $url = "https://trustpositif.kominfo.go.id/Rest_server/getrecordsname_home";
        // $url = "https://indiwtf.com/api/check?domain=$data->domain&token=$token";
        // $curl = curl_init($url);
        // curl_setopt($curl, CURLOPT_URL, $url);
        // curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // $res = curl_exec($curl);
        // curl_close($curl);
        // if ($data->jenis != 3) {
            $result = shell_exec('curl -X POST -d "name=' . $data->domain . '" https://trustpositif.kominfo.go.id/Rest_server/getrecordsname');
            // $result = json_decode($res);
        // } else {
            // $res = json_decode($res);
            // $data = new stdClass();
            // $status = new stdClass();
            // $data = $res->data;
            // $res->status = "backup";
            // $status->status = "backup";
            // $data->data = $status;
            // $result = $res;
        // }
        return response()->json($result, 200);
    }

    public function get_websites() {
        if (session()->get('id_bo') == 0) {
            $get = DB::table('websites')
                ->join('daftar_bo', 'websites.nama_bo', '=', 'daftar_bo.id')
                ->select('websites.id AS id', 'domain', 'daftar_bo.nama_bo AS nama_bo', 'kontak', 'websites.jenis')
                ->where('websites.jenis', 1) // MS Utama
                ->orWhere('websites.jenis', 2) // Redirector
                // ->orWhere('websites.jenis', 3) // MS Cadangan

                ->get();
        } else {
            $get = DB::table('websites')
                ->join('daftar_bo', 'websites.nama_bo', '=', 'daftar_bo.id')
                ->select('websites.id AS id', 'domain', 'daftar_bo.nama_bo AS nama_bo', 'kontak', 'websites.jenis')
                ->where('websites.nama_bo', session()->get('id_bo'))
                ->where('websites.jenis', 1) // MS Utama
                ->orwhere('websites.nama_bo', session()->get('id_bo'))
                ->where('websites.jenis', 2) // Redirector
                ->orwhere('websites.nama_bo', session()->get('id_bo'))
                ->where('websites.jenis', 3) // MS Cadangan
                ->orderBy('websites.jenis', 'asc')
                ->get();
        }
        return $get;
    }

    public function bot_check() {
        $token = "6303246029:AAF50OQAHnNxfiYbNjwTZaDhbpCifMVY4xg";
        $chat_id = session()->get('id_telegram');

        $data = request()->content;
        $message = $this->bot_message($data);
        $telegram = new Telegram($token);
        $content = array('chat_id' => $chat_id, 'text' => $message, 'parse_mode' => 'HTML');
        return response()->json($telegram->sendMessage($content), 200);
    }

    public function bot_tele_redirect($data) {
        $token = "6303246029:AAF50OQAHnNxfiYbNjwTZaDhbpCifMVY4xg";
        $chat_id = session()->get('id_telegram');

        $message = "\n<b>INFO REDIRECTOR</b>\n";
        $message .= $data;
        $telegram = new Telegram($token);
        $content = array('chat_id' => $chat_id, 'text' => $message, 'parse_mode' => 'HTML');
        return response()->json($telegram->sendMessage($content), 200);
    }

    public function bot_message($data) {
        $message = "<b>NAWALA INFO</b>\n";

        $message .= "\n<b>MS UTAMA:</b>\n";
        foreach ($data as $key):
            if ($key['jenis'] == 1) {
                // START DEBUG
                // $key['status'] = 'blocked';
                // END DEBUG
                if ($key['status'] == 'allowed') {
                    $status = '✅ ';
                } else if ($key['status'] == 'blocked') {
                    $status = '❌ ';
                    $cadangan_dipakai = $this->redirect_ms($key);
                } else if ($key['status'] == 'backup') {
                    $status = '';
                } else {
                    $status = '⚠️ ';
                }
                $message .= "$status{$key['domain']}\n";
            }
        endforeach;
        $message .= "\n<b>REDIRECTOR:</b>\n";
        foreach ($data as $key) :
            if ($key['jenis'] == 2) {
                if ($key['status'] == 'allowed') {
                    $status = '✅ ';
                } else if ($key['status'] == 'blocked') {
                    $status = '❌ ';
                    $this->redirect_ms($key);
                } else if ($key['status'] == 'backup') {
                    $status = '';
                } else {
                    $status = '⚠️ ';
                }
                $message .= "$status{$key['domain']}\n";
            }
        endforeach;
        $message .= "\n<b>MS CADANGAN:</b>\n";
        foreach ($data as $key) :
            if ($key['jenis'] == 3) {
                if ($key['status'] == 'allowed') {
                    $status = '✅ ';
                } else if ($key['status'] == 'blocked') {
                    $status = '❌ ';
                    $this->redirect_ms($key);
                } else if ($key['status'] == 'backup') {
                    $status = '';
                } else {
                    $status = '⚠️ ';
                }
                if (isset($cadangan_dipakai)) {
                    if ((int)$key['id'] == $cadangan_dipakai)
                        $message .= "$status<i><s>{$key['domain']}</s></i>\n";
                    else
                        $message .= "$status{$key['domain']}\n";
                } else {
                    $message .= "$status{$key['domain']}\n";
                }
            }
        endforeach;
        return $message;
    }

    public function add_domain() {
        $data = json_decode(request()->data);
        $find = ['http://', 'https://', 'www.', '/'];
        $domain_name = str_replace($find, '', $data->domain);
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
            DB::table('websites')->insert(['domain' => $domain_name, 'nama_bo' => $data->namaBO, 'jenis' => $data->jenis]);
            return response()->json('ok', 200);
        }
    }

    public function ubah_domain() {
        $data = json_decode(request()->data);
        $find = ['http://', 'https://', 'www.', '/'];
        $domain_name = str_replace($find, '', $data->domain);
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
            DB::table('websites')->where('id', $data->idDomain)->update(['domain' => $domain_name, 'jenis' => $data->jenis]);
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

    public function redirect_ms($data) {
        $id = (int)$data['id'];

        $web = DB::table('websites')
            ->select('*')
            ->where('id', $id)
            ->first();

        // cari domain cadangan
        $cadangan = DB::table('websites')
                ->select('*')
                ->where('nama_bo', $web->nama_bo)
                ->where('jenis', 3)
                ->first();

        if ($cadangan == null) {
            $this->bot_tele_redirect('Tidak Ada MS Cadangan');
        } else {
            $redirector = DB::table('redirectors')
                ->join('websites', 'websites.id', '=', 'redirectors.domain')
                ->select('websites.domain AS web_domain', 'websites.id AS id', 'redirectors.id AS red_id')
                ->where('redirect', $id)
                ->get();

            foreach ($redirector as $redirect):
                // START DEBUGGING
                // $redirect->web_domain = 'jualcabe.pro';
                // $cadangan->domain = 'cabemaniswkwk.com';
                // $cadangan->id = 18;
                // END DEBUGGING

                $page_rule_set = new Pagerule();
                $page_rule_info = $page_rule_set->index($redirect->web_domain, $cadangan->domain);
                DB::table('redirectors')->where('id', $redirect->red_id)->update(['redirect' => $cadangan->id]);
                DB::table('websites')->where('id', $id)->update(['jenis' => 4]);
                DB::table('websites')->where('id', $cadangan->id)->update(['jenis' => 1]);
                // return response()->json("Redirector $redirect->web_domain mengarah ke $cadangan->domain.", 200);
                $message = "Redirector $redirect->web_domain mengarah ke $cadangan->domain.";
                $this->bot_tele_redirect($message);
            endforeach;
            return $cadangan->id;
        }
    }

    private function add($date_str, $months) {
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
