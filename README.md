<h1 align="center"> discern </h1>

<p align="center"> A service that identifies the key information of the invoice.</p>


## 环境需求

- PHP >= 5.6

## 安装

```shell
$ composer require "lyignore/discern"
```

## 使用

```php
use Lyignore\Discern\Invoice;

$config = [
    // HTTP 请求的超时时间（秒）
    'timeout' => 5.0,

    // 认证发送配置
    'default' => [
        'appid'  => 'dmeUqS4+M3aH1A5u+v1eogB3d69PwTKN',
        'secret' => 'LoDPHC83A5tRlcn/wnlwmEgEnSuBeC4U'
    ],
    // 主域名配置
    'host_url' => [
        //是否正式上线
        'online' => false,
        //非稳定测试yuming
        'beta_url' => 'http://192.168.2.177:8844',
        //正式计费域名
        'lts_url'=> 'http://tmpl.aikaka.com.cn'
    ],
];

$invoice = new Invoice($config);
//上传图片
$img = $invoice->uploadInvoice('./web/static/imgs/bg.jpg');
var_dump($img);

//获取图片详情
$imgId = $img['data']['md5'];
$imgInfo = $invoice->getInvoice($imgId);
var_dump($imgInfo);

//按时间获取上传的发票列表
$imgList = $invoice->getList('1550703352', '1550733352', 'taxi');
var_dump($imgList);

//批量导出发票信息
$invoiceExcel = $invoice->exportInovices('1550703352', '1550733352', 'taxi', 'excelname');
```
## License

MIT