<?php


namespace App\Utility;


use App\Utility\Markdown\Parser;
use App\Utility\Markdown\ParserResult;
use EasySwoole\EasySwoole\Config;

class HtmlTemplateBuilder
{
    public static function build(ParserResult $result,string $lan)
    {
        $docPath = Config::getInstance()->getConf('DOC.PATH');
        $sidebarPath = "{$docPath}/{$lan}/sidebar.md";
        //获取sideBar的parserHtml
        $sideBarResult = Parser::htmlWithLinkHandel($sidebarPath);
        $global = file_get_contents("{$docPath}/global.tpl");
        //获取配置项
        $config = $result->getConfig();
        $globalConfigResult = Parser::htmlWithLinkHandel("{$docPath}/{$lan}/globalConfig.md");
        $globalConfig = $globalConfigResult->getConfig();
        $configHtml = self::headConfig2Html($config,$globalConfig);
        return str_replace(['{$header}', '{$sidebar}', '{$content}', '{$lan}'], [$configHtml , $sideBarResult->getHtml(), $result->getHtml(), $lan], $global);
    }

    protected static function headConfig2Html(array $config, array $globalConfig)
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
}