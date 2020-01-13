<?php


namespace App\HttpController;


use App\Utility\Markdown\File;
use App\Utility\Markdown\Parser;
use App\Utility\Markdown\ParserResult;
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
        $filePath = File::getFilePath($path);
        //不存在文件则报错404
        if (!file_exists($filePath)) {
            $this->response()->withStatus(Status::CODE_NOT_FOUND);
            $file = EASYSWOOLE_ROOT . '/vendor/easyswoole/easyswoole/src/Resource/Http/404.html';
            if (!is_file($file)) {
                $file = EASYSWOOLE_ROOT . '/src/Resource/Http/404.html';
            }
            $this->response()->write(file_get_contents($file));
            return true;
        }
        $result = Parser::parserToHtml($filePath);

        if ($this->request()->getMethod() == 'POST') {
            $this->writeJson(Status::CODE_OK, $result, 'success');
        } else {
            $this->response()->withStatus(Status::CODE_OK);
            $this->response()->write($result->getHtml());
            \EasySwoole\Utility\File::createFile(EASYSWOOLE_ROOT.'/Temp/'.$path,$result->getHtml());
        }
    }
}