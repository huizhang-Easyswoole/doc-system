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
class Keyword
{
    static function cacheKeyword()
    {
        self::genJson('Cn');
        self::genJson("En");
    }

    static function genJson($lan){
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
                'path'  => Parser::changeLink($path),
                'child' => self::getChildKeyword($path, $lan)
            ];
        }
        $jsonPath = EASYSWOOLE_ROOT."/Static/keyword{$lan}.json";

        File::createFile($jsonPath,json_encode($jsonList));
    }

    static function getSidebar($lan = 'Cn')
    {
        $docPath = Config::getInstance()->getConf('DOC.PATH');

        $sidebarPath = "{$docPath}/{$lan}/sidebar.md";
        //获取sideBar的parserHtml
        $sideBarResult = Parser::parserToHtml($sidebarPath);
        $html = $sideBarResult->getHtml();
        return $html;
    }


    static function getChildKeyword($path, $lan = 'Cn')
    {
        $keywordList = [];
        //获取页面的所有关键字
        $docPath = Config::getInstance()->getConf('DOC.PATH');
        $filePath = "{$docPath}/$lan/{$path}";
        if (!file_exists($filePath)) {
            return null;
        }
        $result = Parser::parserToHtml($filePath);
        $html = $result->getHtml();

        $dom = HtmlDomParser::str_get_html($html);
        $hList = $dom->find('h1,h2,h3,h4,h5,h6');
        foreach ($hList as $h) {
            $keywordList[] = [
                'name' => $h->getNode()->textContent,
                'path' => Parser::changeLink($path) . "#" . $h->getNode()->textContent,
            ];
        }
        return $keywordList;
    }
}