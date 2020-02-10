<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 20-2-10
 * Time: 上午11:29
 */

include "./vendor/autoload.php";
\EasySwoole\EasySwoole\Core::getInstance()->initialize();
\App\Utility\Keyword::cacheKeyword();
