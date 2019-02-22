<?php
namespace Lyignore\Discern;

use GuzzleHttp\Client;
use Lyignore\Discern\Support\Config;

class Messenger{
    const STATUS_SUCCESS = 'success';

    const STATUS_FAILURE = 'failure';

    const ENDPOINT_FORMAT = 'JSON';

    const ENDPOINT_SIGNATURE_VERSION = '1.0';

    const INVOICE_DISCERN = '/invoice/recongnition/binary';

    const TICKET_DISCERN = '/ticket/recongnition/binary';

    const IDCARD_DISCERN = '/idcard/recongnition/binary';

    const TAXI_DISCERN = '/taxi/recongnition/binary';

    const PLANE_DISCERN = '/plane/recongnition/binary';

    const DISCERN_TYPE = ['INVOICE', 'TICKET', 'IDCARD', 'TAXI', 'PLANE'];

    protected $timeout = '5.0';

    protected $base_uri;

    protected $config;

    public function __construct(Config $config){
        $this->config = $config->get('timeout');
        if($config->get('host_url.online')){
            $this->base_uri = $config->get('host_url.lts_url');
        }else{
            $this->base_uri = $config->get('host_url.beta_url');
        }
    }

    /*
     * 上传识别发票
     */
    public function invoiceDiscern(File $file, $type){
        $type = strtoupper($type);
        if(in_array($type, self::DISCERN_TYPE)){
            $relative = $type.'_DISCERN';
            $base_uri = $this->base_uri.$this->$relative;
        }else{
            $base_uri = $this->base_uri.self::INVOICE_DISCERN;
        }
        //$uploadFile = new \CURLFile($file->putFile());
        $client = new Client();
        $res = $client->request('POST', $base_uri, [
            'timeout' => $this->timeout,
            'multipart' => [
                [
                    'name'     => 'file',
                    'contents' => fopen($file->putFile(), 'r')
                ],
            ]
        ]);
        if($res->getStatusCode() == 200){
            $body = $res->getBody();
            return $body->getContents();
        }else{
            throw new FileException('Internet image acquisition failed');
        }
    }


    protected function generateSign(array $params){
        ksort($params);
        $accessKeySecret = $this->config->get('default.secret');
        $stringToSign = urlencode(http_build_query($params));

        return base64_encode(hash_hmac('sha1', $stringToSign, $accessKeySecret.'&',  true));
    }

    protected function getTimestamp(){
        $timezone = date_default_timezone_get();
        date_default_timezone_set('GMT');
        $timestamp = date('Y-m-d H:i:s');
        date_default_timezone_set($timezone);
        return $timestamp;
    }
}