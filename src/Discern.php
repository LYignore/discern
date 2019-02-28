<?php
namespace Lyignore\Discern;

use Carbon\Carbon;
use Lyignore\Discern\Exceptions\MessengerException;
use Lyignore\Discern\Support\Config;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Discern {
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
    public function exportInovices($start_time, $end_time, $type, $excelname='demo'){
        $result = $this->getList($start_time, $end_time, $type);
        if($result['return_code'] == 200){
            $content = $result['data']['list']??[];
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $keys = [
                'getof_time',
                'distance',
                'boarding_time',
                'other',
                'unit_price',
                'tel',
                'certificate_no',
                'taxi_no',
                'issued_date',
                'total',
                'waiting_time',
                'invoice_code',
                'invoice_number',
                'duplication_checking',
                'invoice_type',
                'status',
                'invoice_id',
                'upload_time',
                'app_id',
                'image_path',
                'image_source',
                'image_status'
            ];
            $AZ = range('A', 'Z');
            foreach ($keys as $key => $value){
                $sheet->getColumnDimension($AZ[$key])->setAutoSize(true);
                $sheet->setCellValue($AZ[$key].'1', $value);
            };

            foreach($content as $k=>$invoices){
                $i = 0;
                foreach($invoices as $invoice){
                    $sheet->setCellValue($AZ[$i].($k+2), $invoice);
                    $i++;
                }
            }

            //$objWrite = IOFactory::createWriter($spreadsheet,'Xls');
            //$objWrite->save('php://output');

            //$objWrite->save('./demo.xlsx');
            $writer = new Xlsx($spreadsheet);
            return $writer->save($excelname.'.xlsx');
        }else{
            return new MessengerException('The network impassability');
        }
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