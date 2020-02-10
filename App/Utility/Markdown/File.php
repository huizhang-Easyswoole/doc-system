<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2020/1/11 0011
 * Time: 14:52
 */

namespace App\Utility\Markdown;


use EasySwoole\EasySwoole\Config;

class File
{
    public static function getFilePath($path, $lan = 'Cn')
    {
        //先将.html改为.md
        $filePath = rtrim($path, "html") . 'md';
        //先看看它前面有没有Cn
        $filePath = '/' . $lan . $filePath;
        $filePath = Config::getInstance()->getConf('DOC.PATH') . $filePath;
        return $filePath;
    }
}