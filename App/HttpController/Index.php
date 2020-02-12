<?php


namespace App\HttpController;


use App\Utility\HtmlTemplateBuilder;
use App\Utility\Markdown\Parser;
use EasySwoole\EasySwoole\Config;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;

class Index extends Controller
{
    public $language;

    /*
    * 语言自动识别
    */
    protected function onRequest(?string $action): ?bool
    {
        $lan = $this->request()->getCookieParams('language');
        if(empty($lan)){
            $lan = $this->request()->getRequestParam('language');
        }
        if(empty($lan)){
            //从用户浏览器ua的accept encode 识别
        }
        $allow = Config::getInstance()->getConf('DOC.LANGUAGE');
        if(in_array($lan,$allow,true)){
            $this->language = $lan;
        }else{
            $this->language = Config::getInstance()->getConf("DOC.DEFAULT_LANGUAGE");
        }
        return true;
    }

    function index()
    {
        $file = EASYSWOOLE_ROOT."/Static/{$this->language}_index.html";
        if(file_exists($file)){
            $this->response()->withAddedHeader('Content-type', 'text/html; charset=utf-8');
            $this->response()->withStatus(Status::CODE_OK);
            $this->response()->write(file_get_contents($file));
        }else{
            $this->response()->write("language {$this->language} is not support yet");
        }
    }

    protected function actionNotFound(?string $action)
    {
        $lan = $this->language;
        $this->language = null;
        $path = $this->request()->getUri()->getPath();
        if (substr($path,-5) =='.html'){
            $filePath = rtrim($path, "html") . 'md';
            $filePath = "/{$lan}".$filePath;
            $filePath = Config::getInstance()->getConf('DOC.PATH') . $filePath;
            if (file_exists($filePath)) {
                $markdownInfo = Parser::htmlWithLinkHandel($filePath);
                if ($this->request()->getMethod() == 'POST') {
                    $this->writeJson(Status::CODE_OK, $markdownInfo, 'success');
                } else {
                    //处理全部的html
                    $this->response()->withAddedHeader('Content-type', 'text/html; charset=utf-8');
                    $this->response()->withStatus(Status::CODE_OK);
                    $this->response()->write(HtmlTemplateBuilder::build($markdownInfo,$lan));
                }
            }else{
                $this->response()->withStatus(Status::CODE_NOT_FOUND);
            }
        }else{
            $this->response()->withStatus(Status::CODE_NOT_FOUND);
        }
    }

    function keyWorldSearch()
    {

    }
}