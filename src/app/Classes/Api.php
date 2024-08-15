<?php

namespace PatrykSawicki\OrlenPaczkaApi\app\Classes;

use GuzzleHttp\Client;
use PatrykSawicki\OrlenPaczkaApi\app\Traits\functions;

class Api
{
    use functions;

    protected static ?string $dynamicApiId = null;
    protected static ?string $dynamicApiKey = null;
    protected static ?bool $sandbox = null;

    protected string $apiId, $apiKey, $url;

    public static function setSandbox(bool $sandbox = true): void
    {
        self::$sandbox = $sandbox;
    }

    public static function setCredentials(string $apiId, string $apiKey): void
    {
        self::$dynamicApiId = $apiId;
        self::$dynamicApiKey = $apiKey;
    }

    public function __construct()
    {
        $sandbox = (self::$sandbox === null) ? config('op.sandbox') : self::$sandbox;

        $this->apiId = (self::$dynamicApiId === null) ? config('op.api_id') : self::$dynamicApiId;
        $this->apiKey = (self::$dynamicApiKey === null) ? config('op.api_key') : self::$dynamicApiKey;

        $this->url = $sandbox ? config('op.sandbox_url') : config('op.api_url');
    }

    /*
     * Send data to API.
     * @param string $route
     * @param array $data
     * @return array
     * */
    protected function postData(string $endpoint, array $data = [], string $resultType = null): array
    {
        $data = $this->makeSoapData(endpoint: $endpoint, data: $this->addAuthData($data));

        /*Send soap data to url*/
        $client = new Client();
        $response = $client->request('POST', $this->url, [
            'headers' => $this->requestHeaders(),
            'body' => $data,
        ]);

        if ($response->getStatusCode() != 200) {
            abort(400, $response->getBody());
        }

        $content = $response->getBody()->getContents();

        $soap = simplexml_load_string($content);
        $response = $soap->children('http://www.w3.org/2003/05/soap-envelope')->Body->children()->{$endpoint . 'Response'};
        $response = is_null($resultType) ? $response->{$endpoint . 'Result'}->Data : $response->{$resultType};

        return json_decode(json_encode($response), true);
    }

    private function addAuthData(array $data): array
    {
        $data['PartnerID'] = $this->apiId;
        $data['PartnerKey'] = $this->apiKey;

        return $data;
    }

    private function makeSoapData(string $endpoint, array $data): string
    {
        $xml = new \SimpleXMLElement(
            '<soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope"></soap12:Envelope>'
        );

        $body = $xml->addChild('soap12:Body');
        $child = $body->addChild($endpoint, null, 'https://91.242.220.103/WebServicePwR');
        foreach ($data as $key => $value) {
            $child->addChild($key, $value);
        }

        /*Return removing html tags*/
        return str_replace("\n", '', $xml->asXML());
    }
}
