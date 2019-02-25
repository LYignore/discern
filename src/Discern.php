<?php
namespace Lyignore\Discern;

use Carbon\Carbon;
use Lyignore\Discern\Support\Config;

class Discern implements \Lyignore\Discern\Contracts\DiscernInterface{
    private $config;

    private $messager;

    private $file;

    public function __construct(array $config){
        $this->config = new Config($config);
        $this->messager = new Messenger($this->config);
    }

    /*
     * 上传发票
     */
    public function uploadInvoice(string $path, $type = 'invoice'){
        //实例化上传对象
        $this->file = new File($path);
        $multipart = [
            [
                'name'     => 'file',
                'contents' => fopen($this->file->putFile(), 'r')
            ],
            [
                'name'     => 'token',
                'contents' => $this->config->get('default.appid')
            ],
        ];
        return $this->messager->sendFile($type.'', $multipart);
    }

    /*
     * 获取识别结果
     */
    public function getInvoice($invoice_id, $type){
        $params = [
            'invoice_id' => $invoice_id
        ];
        return $this->messager->getInfo($type, $params);
    }

    /*
     * 获取上传发票的列表
     */
    public function getList($start_time, $end_time, $type){
        if(!($start_time instanceof Carbon)){
            $start_time = new Carbon($this->isData($start_time)?:date('Y-m-d 00:00:00', time()));
        }
        if(!($end_time instanceof Carbon)){
            $end_time = new Carbon($this->isData($end_time)?:date('Y-m-d 23:59:59', time()));
        }
        $params = [
            'time_from' => $start_time->toDateTimeString(),
            'time_to'   => $end_time->toDateTimeString(),
            'skip'      => 0,
            'size'      => 10,
            'app_id'    => $this->config->get('default.appid')
        ];
        return $this->messager->postList($type, $params);
    }

    /*
     * 批量导出发票信息
     */
    public function exportInovices(){

    }

    /*
     * 格式化日期，判断是否正确
     */
    protected function isData($timestamp, $format = 'Y-m-d H:i:s'){
        if(!is_numeric($timestamp) || strlen($timestamp) != 10) return false;
        $timezone = date_default_timezone_get();
        date_default_timezone_set('Asia/Shanghai');
        $timestamps = date('Y-m-d H:i:s',$timestamp);
        date_default_timezone_set($timezone);
        return $timestamps;
    }
}