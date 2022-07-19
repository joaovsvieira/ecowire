<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class IPagClient
{
    public function __construct(protected string $endpoint, protected string $username, protected string $password) { }

    public function paymentIntents($params = null)
    {
        self::_validateParams($params);
        $url = $this->endpoint . '/service/payment';

        $response = Http::withBasicAuth($this->username, $this->password)
                ->withHeaders([ 'x-api-version' => 2 ])
                ->post($url, $params);

        return $response->json();
    }

    protected static function _validateParams($params = null)
    {
        if ($params && !\is_array($params)) {
            $message = 'You must pass an array as the first argument to iPag API
                (HINT: an example call to create a charge ' .
                "would be: \"IPagClient::create(['amount' => 100]))";
            throw new \Exception($message);
        }
    }
}
