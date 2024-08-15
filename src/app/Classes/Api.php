<?php

namespace PatrykSawicki\OrlenPaczkaApi\app\Classes;

use GuzzleHttp\Client;
use PatrykSawicki\OrlenPaczkaApi\app\Traits\functions;
use SimpleXMLElement;

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
        $sandbox = self::$sandbox ?? config('op.sandbox');

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
    protected function postData(string $endpoint, array $data = [], string $resultType = null, bool $parseData = false): array
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

        if ($parseData) {
            return $this->parseData($content);
        }

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

    protected function parseData(string $data): array
    {
        $sxe = new SimpleXMLElement($data);
        $sxe->registerXPathNamespace('d', 'urn:schemas-microsoft-com:xml-diffgram-v1');

        $result = $sxe->xpath("//NewDataSet");

        return json_decode(json_encode($result[0]), true);
    }

    /*
     * Faking Send data to API.
     * @param string $route
     * @param array $data
     * @return array
     * */
    protected function fakePostData(string $endpoint, array $data = [], string $resultType = null, bool $parseData = false): array
    {
        $content = '<?xml version="1.0" encoding="utf-8"?>
            <soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:xsd="http://www.w3.org/2001/XMLSchema">
            <soap:Body>
                <GenerateBusinessPackResponse xmlns="https://91.242.220.103/WebServicePwR">
                <GenerateBusinessPackResult>
                    <xs:schema id="NewDataSet"
                    xmlns=""
                    xmlns:xs="http://www.w3.org/2001/XMLSchema"
                    xmlns:msdata="urn:schemas-microsoft-com:xml-msdata">
                    <xs:element name="NewDataSet" msdata:IsDataSet="true" msdata:UseCurrentLocale="true">
                        <xs:complexType>
                        <xs:choice minOccurs="0" maxOccurs="unbounded">
                            <xs:element name="GenerateBusinessPack">
                            <xs:complexType>
                                <xs:sequence>
                                <xs:element name="Err" type="xs:string" minOccurs="0" />
                                <xs:element name="ErrDes" type="xs:string" minOccurs="0" />
                                <xs:element name="PackCode_RUCH" type="xs:string" minOccurs="0" />
                                <xs:element name="DestinationCode" type="xs:string" minOccurs="0" />
                                <xs:element name="DestinationId" type="xs:int" minOccurs="0" />
                                <xs:element name="PackPrice" type="xs:int" minOccurs="0" />
                                <xs:element name="PackPaid" type="xs:boolean" minOccurs="0" />
                                </xs:sequence>
                            </xs:complexType>
                            </xs:element>
                        </xs:choice>
                        </xs:complexType>
                    </xs:element>
                    </xs:schema>
                    <diffgr:diffgram xmlns:msdata="urn:schemas-microsoft-com:xml-msdata"
                    xmlns:diffgr="urn:schemas-microsoft-com:xml-diffgram-v1">
                    <NewDataSet xmlns="">
                        <GenerateBusinessPack diffgr:id="GenerateBusinessPack1" msdata:rowOrder="0">
                        <Err>000</Err>
                        <ErrDes>saved</ErrDes>
                        <PackCode_RUCH>2101066592589</PackCode_RUCH>
                        <DestinationCode>BD-158471-NN-14</DestinationCode>
                        <DestinationId>158471</DestinationId>
                        <PackPrice>999</PackPrice>
                        <PackPaid>true</PackPaid>
                        </GenerateBusinessPack>
                    </NewDataSet>
                    </diffgr:diffgram>
                </GenerateBusinessPackResult>
                </GenerateBusinessPackResponse>
            </soap:Body>
            </soap:Envelope>';

        if ($parseData) {
            return $this->parseData($content);
        }

        $soap = simplexml_load_string($content);
        $response = $soap->children('http://www.w3.org/2003/05/soap-envelope')->Body->children()->{$endpoint . 'Response'};
        $response = is_null($resultType) ? $response->{$endpoint . 'Result'}->Data : $response->{$resultType};

        return json_decode(json_encode($response), true);
    }
}
