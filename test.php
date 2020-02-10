<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 20-2-10
 * Time: 上午11:29
 */

include "./vendor/autoload.php";
\EasySwoole\EasySwoole\Core::getInstance()->initialize();
$json = file_get_contents(EASYSWOOLE_ROOT . '/Static/1.json');

$list = json_decode($json, 1);
$txt = '';
$level = 0;
a($txt,$list);

var_dump($txt);
function a(&$txt,$list,$level=0){
    foreach ($list as $value) {
        //标准链接
        if (isset($value[0]) && isset($value[1]) && empty($value['children'])) {
            $html = "- [{$value[1]}]({$value[0]}.md)";
        } else {
            $html = "- " . trim($value['title']);
        }
        addTxt($txt, $level, $html);
        //如果是有子类的,则继续循环子类
        if (!empty($value['children'])){
            a($txt,$value['children'],$level+1);
        }
    }
}


function addTxt(&$txt, $level, $html)
{
    $space = str_repeat(" ", ($level*4));
    $txt .= $space . $html . "\n";

}

