<?php

namespace Jmalko\CfStream\Tags;

use Statamic\Tags\Tags;

class CfStream extends Tags {

    var $account_id, $subdomian, $api_key, $creator_id;

    public function __construct() {
        $this->account_id = config('cf-stream.account_id');
        $this->subdomain = config('cf-stream.subdomain');
        $this->api_key = config('cf-stream.api_key');
        $this->creator_id = config('cf-stream.creator_id');
    }

    public function embed() {

       $uid = $this->params->get('id');

       if (!$uid) {
           return;
       }

$html = <<<HTML
<div style="position: relative; padding-top: 56.25%;">
  <iframe
    src="https://{$this->subdomain}/{$uid}/iframe?poster=https%3A%2F%2F{$this->subdomain}%2F{$uid}%2Fthumbnails%2Fthumbnail.jpg%3Ftime%3D%26height%3D600"
    style="border: none; position: absolute; top: 0; left: 0; height: 100%; width: 100%;"
    allow="accelerometer; gyroscope; autoplay; encrypted-media; picture-in-picture;"
    allowfullscreen="true"
  ></iframe>
</div>
HTML;

       return $html;

    }

    public function proper_embed() {

       $uid = $this->params->get('id');

       if (!$uid) {
           return;
       }

       $curl = curl_init();

        curl_setopt_array($curl, [
          CURLOPT_URL => "https://api.cloudflare.com/client/v4/accounts/$this->account_id/stream/$uid/embed",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "Authorization: bearer $this->api_key"
          ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          return "cURL Error #:" . $err;
        } else {
          return "<div style='width: 500px; height: 282px;'>$response</div>";
        } 
    }

}
