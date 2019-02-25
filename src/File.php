<?php
namespace Lyignore\Discern;

use GuzzleHttp\Client;
use Lyignore\Discern\Contracts\FileInterface;
use Lyignore\Discern\Exceptions\FileException;

class File implements FileInterface{
    const FILE_ENCODING = 'UTF8';

    protected $path;

    protected $type;

    protected $content;

    protected $size;

    protected $imgPath;

    public function __construct(string $path){
        //判断是否是网络地址
        if($this->checkNetwork($path)){
            $this->path = $path;
            $this->getImageStr($path);
        }else{
            if(file_exists($path)){
                $this->path = $path;
                $this->content = file_get_contents($path);
            }elseif(file_exists(__DIR__.'/'.$path)){
                $this->path = __DIR__.'/'.$path;
                $this->content = file_get_contents($path);
            }elseif(file_exists(getcwd().'/'.$path)){
                $this->path = getcwd().'/'.$path;
                $this->content = file_get_contents($path);
            }else{
                throw new FileException('There is a problem with the picture address');
            }
        }
    }

    /*
     * 检测网络地址
     */
    public function checkNetwork(string $path){
        if(preg_match("/^(https?\:\/\/)/i", $path)){
            return true;
        }else{
            return false;
        }
    }

    /*
     * 获取图片数据流
     */
    protected function getImageStr(string $path){
        $client = new Client();
        try{
            $res = $client->request('GET', $path, ['verify' => false]);
            if($res->getStatusCode() == 200){
                $body = $res->getBody();
                $this->content = $body->getContents();
            }else{
                throw new FileException('Internet image acquisition failed');
            }
        }catch (\Exception $e){
            throw new FileException('Internet image acquisition failed');
        }
    }

    /*
     * 判断文件是否存在
     */
    public function checkExist(){
        if(file_exists($this->path)){
            $this->content = file_get_contents($this->path);
            return true;
        }else{
            return false;
        }
    }

    public function getPath(){
        return $this->path;
    }

    public function getContent(){
        return $this->content;
    }

    public function setFileSize(){
        $this->size = mb_strlen($this->content, self::FILE_ENCODING);
    }

    public function getFileSize(){
        return $this->size;
    }

    public function putFile(){
        $filepath = dirname(__DIR__).'/uploads/'.time().mt_rand(1000, 9999);
        $myfile = fopen($filepath, "w") or die("Unable to open file!");
        fwrite($myfile, $this->content);
        fclose($myfile);
        //file_put_contents($filepath, $this->content);
        $this->imgPath = realpath($filepath);
        return $this->imgPath;
    }
    public function __destruct(){
        unlink($this->imgPath);
    }
}