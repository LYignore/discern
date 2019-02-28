<?php
require __DIR__ .'/../vendor/autoload.php';

use Lyignore\Discern\Discern;
$config = [
    // HTTP 请求的超时时间（秒）
    'timeout' => 5.0,

    // 认证发送配置
    'default' => [
        //'appid'  => '99rR7E9LtiFOdWwb8NE5UrrovtP8uJMq',
        'appid'  => 'dmeUqS4+M3aH1A5u+v1eogB3d69PwTKN',
        'secret' => 'LoDPHC83A5tRlcn/wnlwmEgEnSuBeC4U'
    ],
    // 主域名配置
    'host_url' => [
        //是否正式上线
        'online' => false,
        //非稳定测试域名
        'beta_url' => 'http://192.168.2.177:8844',
        //正式计费域名
        'lts_url'=> 'https://bdp.aikaka.cc/IRSPAPP'
    ],
];
$invoice = new Discern($config);
//上传识别本地图片或者线上图片
//$invoiceStr = $invoice->uploadInvoice('img/5670245040416219.jpg');
$invoiceStr = $invoice->uploadInvoice('https://download.aikaka.com.cn/f0328e346f521339086089b73141839f', 'TAXI');
var_dump($invoiceStr);
//{
//    "return_code": 200,
//    "return_msg": "",
//    "data": {
//        "md5": "b5d6843e7bd02f36f82d889e033ba701"
//    }
//}

//获取发票详情
//$md5 = 'b5d6843e7bd02f36f82d889e033ba701';
//$type = 'taxi';
//$invoiceInfo = $invoice->getInvoice($md5, $type);
//var_dump($invoiceInfo);


//通过时间戳获取appid用户一段时间的上传发票详情
//$start_time = '1551024000';
//$end_time = '1551092040';
//$type = 'taxi';
//$invoiceList = $invoice->getList($start_time, $end_time, $type);
//var_dump($invoiceList);

//导出一段时间的数据至Excel
//$start_time = '1551024000';
//$end_time = '1551092040';
//$type = 'taxi';
//$excelname = 'demo';
//$invoiceExcel = $invoice->exportInovices($start_time, $end_time, $type, $excelname);
