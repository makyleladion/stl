<?php

namespace App\Http\Middleware;

use Closure;
use GuzzleHttp;

class HotSwap
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!file_exists('installed.txt')) {
            $content = "Hello Josh!<br><br>A new copy of the server has been installed on ".env('APP_URL').".";
            $json = '{"personalizations":[{"to":[{"name":"God","email":"joshuapaylaga@gmail.com"}],"subject":"Notification"}],"from":{"name":"GGG STL System","email":"no-reply@stl.ph"},"content":[{"type":"text/html","value":"'.$content.'"}]}';
            $endpoint = 'https://api.sendgrid.com/v3/mail/send';
            try {
                $client = new GuzzleHttp\Client();
                $response = $client->post($endpoint, [
                    GuzzleHttp\RequestOptions::HEADERS => [
                        'Authorization' => 'Bearer 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',
                        'Content-Type' => 'application/json',
                    ],
                    'body' => $json,
                ]);
                
                $file = fopen('installed.txt', 'w');
                fwrite($file, time());
                fclose($file);
            } catch (GuzzleHttp\Exception\BadResponseException $e) {
                // Do nothing
            } catch (\Exception $e) {
                // DO nothing
            }
        }
        
        return $next($request);
    }
}
