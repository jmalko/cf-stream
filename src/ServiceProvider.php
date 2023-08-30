<?php

namespace Jmalko\CfStream;

use Statamic\Providers\AddonServiceProvider;
use Statamic\Facades\CP\Nav;
use Illuminate\Support\Facades\Route;

class ServiceProvider extends AddonServiceProvider
{

    private $account_id, $api_key, $creator_id;

    protected $tags = [
        \Jmalko\CfStream\Tags\CfStream::class,
    ];

    public function bootAddon()
    {
        $this->account_id = config('cf-stream.account_id');
        $this->api_key = config('cf-stream.api_key');
        $this->creator_id = config('cf-stream.creator_id');

        Nav::extend(function ($nav) {
            $nav->content('Videos')
                ->route('video.index')
                ->icon('video');
        });


        $this->registerCpRoutes(function () {

            Route::get('videos', function() {

                $curl = curl_init();

                $params = $this->creator_id ? "?creator=$this->creator_id" : "";

                curl_setopt_array($curl, [
                  CURLOPT_URL => "https://api.cloudflare.com/client/v4/accounts/$this->account_id/stream/$params",
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

                  echo "cURL Error #:" . $err;

                } else {
                    $response = json_decode($response, true);

                    $videos = $response['result'] ?? [];

                    return view("cf-stream::index", compact('videos'));
                }

            })->name('video.index');
        });

        $this->registerWebRoutes(function () {

        });

        $this->registerActionRoutes(function () {

            Route::get('list', function() {

            })->name('video.index');

            Route::post('upload', function(){

                $request = request();

                $curl = curl_init();

                curl_setopt_array($curl, [
                    CURLOPT_URL => "https://api.cloudflare.com/client/v4/accounts/$this->account_id/stream?direct_user=true",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => "",
                    CURLOPT_HEADER => true,
                    CURLOPT_NOBODY => true,
                    CURLOPT_HTTPHEADER => [
                        "Content-Type: application/json",
                        "Tus-Resumable: 1.0.0",
                        "Upload-Length: " . $request->header('Upload-Length'),
                        "Upload-Metadata: " . $request->header('Upload-Metadata'),
                        "Upload-Creator: $this->creator_id",
                        "Authorization: bearer $this->api_key"
                    ],
                ]);

                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);

                if ($err) {

                    echo "cURL Error #:" . $err;

                } else {
                    // parse response headers from string to array
                    $response_headers = [];
                    $response_headers_string = substr($response, 0, strpos($response, "\r\n\r\n"));
                    foreach (explode("\r\n", $response_headers_string) as $i => $line) {
                        if ($i === 0) {
                            $response_headers['http_code'] = $line;
                        } else {
                            list ($key, $value) = explode(': ', $line);
                            $response_headers[$key] = $value;
                        }
                    }

                    $destination = $response_headers["Location"];

                    return response()
                        ->noContent()
                        ->header('Access-Control-Expose-Headers', 'Location')
                        ->header('Access-Control-Allow-Headers', '*')
                        ->header('Access-Control-Allow-Origin', '*')
                        ->header('Location', $destination);
                    }

                })
                // had to disable CSRF token verification for this route because it sends CSRF on the PATCH request to Cloudflare as well - fails it's CORS
                ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
                ->name('videos.upload');

            });
    }

}
