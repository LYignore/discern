<?php
namespace Lyignore\Discern;

use Lyignore\Discern\Support\Config;

class Invoice implements \Lyignore\Discern\Contracts\InvoiceInterface{
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
        return $this->messager->invoiceDiscern($this->file, $type);
    }

    /*
     * 获取识别结果
     */
    public function getInvoice(){

    }

    /*
     * 获取上传发票的列表
     */
    public function getList(){

    }

    /*
     * 批量导出发票信息
     */
    public function exportInovices(){

    }
}