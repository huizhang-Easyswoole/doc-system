<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 20-2-10
 * Time: 上午10:24
 */

namespace App\Utility;

use App\Utility\Markdown\Parser;
use EasySwoole\EasySwoole\Config;
use EasySwoole\Utility\File;
use voku\helper\HtmlDomParser;
use voku\helper\SimpleHtmlDom;

/**
 * 存储关键字数据
 * Class Keyword
 * @package App\Utility
 */
class DocKeyworldsParser
{
    static function scan()
    {
        self::parserFiles2JsonUrlMap('Cn');
        self::parserFiles2JsonUrlMap("En");
    }

    protected static function parserFiles2JsonUrlMap($lan){
        $jsonList = [];
        $sidebarHtml = self::getSidebar($lan);
        $dom = HtmlDomParser::str_get_html($sidebarHtml);
        $aList = $dom->find('a');
        /**
         * @var $a SimpleHtmlDom
         */
        foreach ($aList as $a) {
            $path = $a->href;
            $name = $a->getNode()->textContent;
            $jsonList[] = [
                'name'  => $name,
                'path'  => Parser::mdLink2Html($path),
                'child' => self::parserHtmlKeyWorld($path, $lan)
            ];
        }
        $jsonPath = EASYSWOOLE_ROOT."/Static/keyword{$lan}.json";

        File::createFile($jsonPath,json_encode($jsonList,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
    }

    protected static function getSidebar($lan = 'Cn')
    {
        $docPath = Config::getInstance()->getConf('DOC.PATH');

        $sidebarPath = "{$docPath}/{$lan}/sidebar.md";
        //获取sideBar的parserHtml
        $sideBarResult = Parser::html($sidebarPath);
        $html = $sideBarResult->getHtml();
        return $html;
    }


    protected static function parserHtmlKeyWorld($path, $lan = 'Cn')
    {
        $keywordList = [];
        //获取页面的所有关键字
        $docPath = Config::getInstance()->getConf('DOC.PATH');
        //这边的path已经存在了/斜杆
        $filePath = "{$docPath}/$lan{$path}";
        if (!file_exists($filePath)) {
            return null;
        }
        $result = Parser::htmlWithLinkHandel($filePath);
        $html = $result->getHtml();

        $dom = HtmlDomParser::str_get_html($html);
        $hList = $dom->find('h1,h2,h3,h4,h5,h6');
        foreach ($hList as $h) {
            $keywordList[] = [
                'name' => $h->getNode()->textContent,
                'path' => Parser::mdLink2Html($path) . "#" . $h->getNode()->textContent,
            ];
        }
        return $keywordList;
    }
}