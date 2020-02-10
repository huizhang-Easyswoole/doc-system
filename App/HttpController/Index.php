<?php


namespace App\HttpController;


use App\Utility\HtmlTemplateBuilder;
use App\Utility\Markdown\Parser;
use App\Utility\Markdown\ParserResult;
use EasySwoole\EasySwoole\Config;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;

class Index extends Controller
{

    function index()
    {
        $file = EASYSWOOLE_ROOT . '/vendor/easyswoole/easyswoole/src/Resource/Http/welcome.html';
        if (!is_file($file)) {
            $file = EASYSWOOLE_ROOT . '/src/Resource/Http/welcome.html';
        }
        $this->response()->write(file_get_contents($file));
    }

    protected function actionNotFound(?string $action)
    {
        $path = $this->request()->getUri()->getPath();
        if (substr($path,-5) =='.html'){
            $filePath = rtrim($path, "html") . 'md';
            $lanStr = substr($path, 1, 2);
            if ($lanStr == 'Cn') {
                $lan = 'Cn';
            } elseif ($lanStr == 'En') {
                $lan = 'En';
            } else {
                $lan = 'Cn';
                $filePath = "/{$lan}".$filePath;
            }
            $filePath = Config::getInstance()->getConf('DOC.PATH') . $filePath;
            if (file_exists($filePath)) {
                $result = Parser::mdFile2Html($filePath);
                if ($this->request()->getMethod() == 'POST') {
                    $this->writeJson(Status::CODE_OK, $result, 'success');
                } else {
                    //处理全部的html
                    $this->html($result, $lan);
                }
            }else{
                $this->response()->withStatus(Status::CODE_NOT_FOUND);
            }
        }else{
            $this->response()->withStatus(Status::CODE_NOT_FOUND);
        }
    }

    protected function html(ParserResult $result, $lan)
    {
        $this->response()->withAddedHeader('Content-type', 'text/html; charset=utf-8');
        $this->response()->withStatus(Status::CODE_OK);
        $this->response()->write(HtmlTemplateBuilder::build($result,$lan));
    }
}