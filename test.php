<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 20-2-10
 * Time: 上午11:29
 */

include "./vendor/autoload.php";
$ret = \App\Utility\Markdown\Parser::parserMdFile('Doc/Cn/Introduction/demo.md');