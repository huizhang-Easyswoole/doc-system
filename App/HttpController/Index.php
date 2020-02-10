<?php


namespace App\HttpController;


use App\Utility\Markdown\File;
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
        if (substr($path,-5)!='.html'){
            $this->return404();
            return true;
        }
        $lanStr = substr($path, 1, 2);
        if ($lanStr == 'Cn') {
            $lan = 'Cn';
        } elseif ($lanStr == 'En') {
            $lan = 'En';
        } else {
            $lan = 'Cn';
        }

        $filePath = File::getFilePath($path, $lan);
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
        $result = Parser::mdFile2Html($filePath);

        if ($this->request()->getMethod() == 'POST') {
            $this->writeJson(Status::CODE_OK, $result, 'success');
        } else {
            //处理全部的html
            $this->html($result, $lan);
        }
    }


    protected function html(ParserResult $result, $lan)
    {
        $docPath = Config::getInstance()->getConf('DOC.PATH');

        $sidebarPath = "{$docPath}/{$lan}/sidebar.md";
        //获取sideBar的parserHtml
        $sideBarResult = Parser::mdFile2Html($sidebarPath);
        //获取其他模板数据
        $nav = file_get_contents("{$docPath}/{$lan}/nav.tpl");
        $footer = file_get_contents("{$docPath}/{$lan}/footer.tpl");
        $global = file_get_contents("{$docPath}/global.tpl");

        //获取配置项
        $config = $result->getConfig();
        $globalConfigResult = Parser::mdFile2Html("{$docPath}/{$lan}/globalConfig.md");
        $globalConfig = $globalConfigResult->getConfig();

        $configHtml = $this->configToHtml($config,$globalConfig);
        $html = str_replace(['{$header}', '{$nav}', '{$sidebar}', '{$content}', '{$footer}', '{$lan}'], [$configHtml , $nav, $sideBarResult->getHtml(), $result->getHtml(), $footer, $lan], $global);

        $this->response()->withAddedHeader('Content-type', 'text/html; charset=utf-8');
        $this->response()->withStatus(Status::CODE_OK);
        $this->response()->write($html);
    }


    protected function configToHtml($config, $globalConfig)
    {
        $html = "";
        $config = [
            'title'=>$config['title']??$globalConfig['title']??'',
            'meta'=>$config['meta']??$globalConfig['meta']??[],
            'base'=>array_merge($config['base']??[],$globalConfig['base']??[]),
            'link'=>array_merge($config['link']??[],$globalConfig['link']??[]),
            'script'=>array_merge($config['script']??[],$globalConfig['script']??[]),
        ];

        //script style
        foreach ($config as $key => $item) {
            if (in_array($key, ['title'])) {
                //只有content的标签
                $html .= "<{$key}>{$item}</{$key}>";
            } else {
                if (in_array($key, ['meta', 'link', 'base'])) {
                    foreach ($item as $value) {
                        $html .= "<{$key}";
                        foreach ($value as $propertyKey => $propertyValue) {
                            //多重标签
                            $html .= " $propertyKey=\"{$propertyValue}\"";
                        }
                        $html .= "/>";
                        $html .= "\n";;
                    }
                } else {
                    //style和script标签
                    foreach ($item as $value) {
                        $html .= "<{$key}";
                        foreach ($value as $propertyKey => $propertyValue) {
                            if ($propertyKey == 'content') {
                                continue;
                            }
                            //多重标签
                            $html .= " $propertyKey=\"{$propertyValue}\"";
                        }

                        $html .= ">" . ($value['content'] ?? '') . "</$key>";
                        $html .= "\n";;
                    }
                }
            }
            $html .= "\n";;
        }
        return $html;
    }


    protected function return404(){
        $this->response()->withStatus(Status::CODE_NOT_FOUND);
        $file = EASYSWOOLE_ROOT . '/vendor/easyswoole/easyswoole/src/Resource/Http/404.html';
        if (!is_file($file)) {
            $file = EASYSWOOLE_ROOT . '/src/Resource/Http/404.html';
        }
        $this->response()->write(file_get_contents($file));
    }


}