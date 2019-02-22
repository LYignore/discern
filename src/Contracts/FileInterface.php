<?php
namespace Lyignore\Discern\Contracts;

interface FileInterface{
    /*
     * 判断文件是否存在可读
     */
    public function checkExist();

    /*
     * 获取文件大小
     */
    public function getFileSize();

    /*
     * 获取文件数据流
     */
    public function getContent();

    /*
     * 获取文件地址
     */
    public function getPath();
}