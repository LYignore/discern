<?php
namespace Lyignore\Discern;

use GuzzleHttp\Client;
use Lyignore\Discern\Support\Config;
use Lyignore\Discern\Exceptions;
use Lyignore\Discern\Traits\HasHttpRequest;

class Messenger{
    use HasHttpRequest;

    const STATUS_SUCCESS = 'success';

    const STATUS_FAILURE = 'failure';

    const ENDPOINT_FORMAT = 'JSON';

    const ENDPOINT_SIGNATURE_VERSION = '1.0';

    const INVOICE_DISCERN = '/invoice/recognition/binary';
    const INVOICE_INFO    = '/invoice/info/test';
    const INVOICE_LIST    = '/invoice/list';

    const TICKET_DISCERN = '/ticket/recognition/binary';
    const TICKET_INFO    = '/ticket/info';
    const TICKET_LIST    = '/ticket/list';

    const IDCARD_DISCERN = '/idcard/recognition/binary';
    const IDCARD_INFO    = '/idcard/info';
    const IDCARD_LIST    = '/idcard/list';

    const TAXI_DISCERN  = '/taxi/recognition/binary';
    const TAXI_INFO     = '/taxi/info';
    const TAXI_LIST     = '/taxi/list';

    const PLANE_DISCERN = '/plane/recognition/binary';
    const PLANE_INFO    = '/plane/info';
    const PLANE_LIST    = '/plane/list';

    const DISCERN_TYPE = ['INVOICE', 'TICKET', 'IDCARD', 'TAXI', 'PLANE'];

    const INTERFACE_TYPE = ['upload', 'info', 'list'];

    protected $timeout = '5.0';

    protected $base_uri;

    protected $config;

    protected $types = 'upload';

    public function __construct(Config $config){
        $this->config = $config;
    }

    /*
     * 查看类中是否有此常量值,查找对应的发送二级域名地址
     */
    protected function getConst(string $string, $type = 'upload'){
        if(in_array($type, self::INTERFACE_TYPE)){
            $path = '';
            switch ($type){
                case 'list':
                    switch ($string){
                        case 'TICKET_LIST':
                            $path = self::TICKET_LIST;
                            break;
                        case 'IDCARD_LIST':
                            $path = self::IDCARD_LIST;
                            break;
                        case 'TAXI_LIST':
                            $path = self::TAXI_LIST;
                            break;
                        case 'PLANE_LIST':
                            $path = self::PLANE_LIST;
                            break;
                        default:
                            $path = self::INVOICE_LIST;
                    }
                    break;
                case 'info':
                    switch ($string){
                        case 'TICKET_INFO':
                            $path = self::TICKET_INFO;
                            break;
                        case 'IDCARD_INFO':
                            $path = self::IDCARD_INFO;
                            break;
                        case 'TAXI_INFO':
                            $path = self::TAXI_INFO;
                            break;
                        case 'PLANE_INFO':
                            $path = self::PLANE_INFO;
                            break;
                        default:
                            $path = self::INVOICE_INFO;
                    }
                    break;
                default:
                    switch ($string){
                        case 'TICKET_DISCERN':
                            $path = self::TICKET_DISCERN;
                            break;
                        case 'IDCARD_DISCERN':
                            $path = self::IDCARD_DISCERN;
                            break;
                        case 'TAXI_DISCERN':
                            $path = self::TAXI_DISCERN;
                            break;
                        case 'PLANE_DISCERN':
                            $path = self::PLANE_DISCERN;
                            break;
                        default:
                            $path = self::INVOICE_DISCERN;
                    }
            }
            return $path;
        }else{
            throw new Exceptions\MessengerException('This interface type does not exist');
        }
    }

    /*
     * 判断当前主域名
     */
    protected function getBaseUri(){
        if($this->config->get('host_url.online')){
            $this->base_uri = $this->config->get('host_url.lts_url');
        }else{
            $this->base_uri = $this->config->get('host_url.beta_url');
        }
        return $this->base_uri;
    }

    /*
     * 配置http过期时间
     */
    protected function getTimeout(){
        $this->timeout = $this->config->get('timeout');
        return $this->timeout;
    }

    /*
     * 配置https不验证证书
     */
    protected function getVerify(){
        return !preg_match("/^(https\:\/\/)/i", $this->base_uri);
    }


    /*
     * 发送文件请求
     */
    public function sendFile($type, $multipart){
        $type = strtoupper($type);
        $point_uri = $this->getConst($type.'_DISCERN', self::INTERFACE_TYPE[0]);
        return $this->postFile($point_uri, $multipart);
//        $client = new Client(['verify' =>false]);
//        $res = $client->request('POST', $base_uri, [
//            'timeout' => $this->timeout,
//            'headers' => [
//                'ticket' => '615b246c4546611a595c62709b97176b'
//            ],
//            'multipart' => $multipart
//        ]);
//        if($res->getStatusCode() == 200){
//            $body = $res->getBody();
//            return $body->getContents();
//        }else{
//            throw new Exceptions\FileException('Internet image acquisition failed');
//        }
    }
    /*
     * 获取详情信息
     */
    public function getInfo($type, $params){
        $point_uri = $this->getConst(strtoupper($type).'_INFO', self::INTERFACE_TYPE[1]);
        return $this->get($point_uri, $params);
    }
    /*
     * 获取列表信息
     */
    public function postList($type, $params){
        $point_uri = $this->getConst(strtoupper($type).'_LIST', self::INTERFACE_TYPE[2]);
        return $this->postJson($point_uri, $params);
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