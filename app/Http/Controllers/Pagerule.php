<?php
namespace App\Http\Controllers;

class Pagerule {
    private $api_key = '7a4b978980278b61fbb362a15fc2b7d8e02e4';
    private $auth_bearer = '2nymyB0zId5Yi3_yYmbCs7BxhKPX9pJQ10jGRYRO';

    public function index($domain_name, $backup_domain) {

        $time = date('d-m-Y h:i:s');

        // Get Zone Id dari $domain_name
        $zone_id = $this->get_zone_id($domain_name);
        if (str_contains($zone_id, 'ERROR'))
            return "[ $time ] $zone_id";

        // Id page rule dari $domain_name
        $page_rule_id = $this->get_pagerule_id($domain_name, $zone_id);

        // Hapus page rule dari $zone_id
        $delete_rule = $this->delete_pagerule($domain_name, $page_rule_id, $zone_id);
        if (str_contains($delete_rule, 'ERROR'))
            return "[ $time ] $delete_rule";

        // Buat page rule untuk $zone_id
        $add_rule = $this->add_pagerule($domain_name, $backup_domain, $zone_id);
        if (str_contains($add_rule, 'ERROR'))
            return "[ $time ] $add_rule";

        return "[ $time ]<br />Page Rule domain <strong>$domain_name</strong> telah diperbarui ke <strong>$backup_domain</strong>.<br /><br />";
    }

    public function get_zone_id($domain_name) {

        /* START CARI ZONE_ID DOMAIN $domain_name */
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.cloudflare.com/client/v4/zones?name=$domain_name",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "X-Auth-Key: $this->api_key",
                "Authorization: Bearer $this->auth_bearer",
                "Content-Type: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err)
            return "<span style='color: red'>ERROR_ZONE_ID</span> <strong>$domain_name</strong><br />$err.";

        $zone_id = json_decode($response);

        if (!$zone_id->success)
            return "<span style='color: red'>ERROR_ZONE_ID</span> <strong>$domain_name</strong><br />" . $zone_id->errors[0]->message . ".";

        if ($zone_id->result == null)
            return "<span style='color: red'>ERROR_ZONE_ID</span> <strong>$domain_name</strong><br />Domain tidak ditemukan.";

        $zone_id = $zone_id->result[0]->id; // Dapatin zone id $domain_name untuk page_rule
        /* END CARI ZONE_ID DOMAIN $domain_name */

        return $zone_id;

    }

    function get_pagerule_id($domain_name, $zone_id) {

        /* START LIST PAGE RULE $domain_name (contoh: black77.biz -> $zone_id=3348d9227a5ab3cce82af3b5e66ea799) */
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.cloudflare.com/client/v4/zones/$zone_id/pagerules",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "X-Auth-Key: $this->api_key",
                "Authorization: Bearer $this->auth_bearer",
                "Content-Type: application/json"
            ],
        ]);
        $page_rule = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err)
            return "<span style='color: red'>ERROR_LIST_PAGE_RULE</span> <strong>$domain_name</strong><br /> $err.";

        $page_rule = json_decode($page_rule);

        $page_rule_id = array();

        foreach ($page_rule->result as $rule):
            array_push($page_rule_id, $rule->id); // Dapatin id page rule untuk di-delete.
        endforeach;
        return $page_rule_id;
        /*  END LIST PAGE RULE $domain_name */

    }

    function delete_pagerule($domain_name, $page_rule_id, $zone_id) {
        foreach ($page_rule_id as $rule) :
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.cloudflare.com/client/v4/zones/$zone_id/pagerules/$rule",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "DELETE",
                CURLOPT_HTTPHEADER => [
                    "X-Auth-Key: $this->api_key",
                    "Authorization: Bearer $this->auth_bearer",
                    "Content-Type: application/json"
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err)
                return "<span style='color: red'>ERROR_DELETE_RULE</span> <strong>$domain_name</strong><br />$err.";

            $delete_rule = json_decode($response);

            if (!$delete_rule->success)
                return "<span style='color: red'>ERROR_DELETE_RULE</span> <strong>$domain_name</strong><br />" . $delete_rule->errors[0]->message . ".";
        endforeach;

        return $response;
    }

    function add_pagerule($domain_name, $backup_domain, $zone_id) {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.cloudflare.com/client/v4/zones/$zone_id/pagerules",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            // CURLOPT_POSTFIELDS => "{\n \"actions\": [\n {\n \"id\": \"forwarding_url\",\n \"status_code\": \"301\"\n, \"url\": \"https://$domain_name/\"\n }\n ],\n \"priority\": 1,\n \"status\": \"active\",\n \"targets\": [\n {\n \"constraint\": {\n \"operator\": \"matches\",\n \"value\": \"www.$domain_name/*\"\n },\n \"target\": \"url\"\n }\n]\n}",
            CURLOPT_POSTFIELDS => "{\"targets\": [{\"target\": \"url\",\"constraint\": {\"operator\": \"matches\",\"value\": \"$domain_name/*\"}}],\"status\": \"active\",\"actions\": [{\"id\": \"forwarding_url\",\"value\": {\"url\": \"https://$backup_domain/$1\",\"status_code\": 301}}]}",
            CURLOPT_HTTPHEADER => [
                "X-Auth-Key: $this->api_key",
                "Authorization: Bearer $this->auth_bearer",
                "Content-Type: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err)
            return "<span style='color: red'>ERROR_ADD_RULE</span> <strong>$domain_name</strong><br />$err.";

        $add_rule = json_decode($response);

        if (!$add_rule->success)
            return "<span style='color: red'>ERROR_ADD_RULE</span> <strong>$domain_name</strong><br />" . $add_rule->messages[0]->message . ".";

        return $response;
    }
}
