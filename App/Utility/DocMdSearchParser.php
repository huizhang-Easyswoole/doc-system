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
 * 文档md文件全文搜索转换
 * Class Keyword
 * @package App\Utility
 */
class DocMdSearchParser
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
        //获取导航栏所有md链接
        $aList = $dom->find('a');
        $id = 1;
        /**
         * @var $a SimpleHtmlDom
         */
        foreach ($aList as $a) {
            $path = $a->href;
            $name = $a->getNode()->textContent;
            $jsonList[] = [
                'id'  => $id,
                'title'  => $name,
                'content'  => self::getMdContent($path),
                'link'  => $path,
            ];
            $id++;
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

    protected static function getMdContent($path,$lan='Cn'){
        $docPath = Config::getInstance()->getConf('DOC.PATH');
        //这边的path已经存在了/斜杆
        $filePath = "{$docPath}/$lan{$path}";

        if (!file_exists($filePath)) {
            return null;
        }
        $result = Parser::htmlWithLinkHandel($filePath);
        $html = $result->getHtml();
        return strip_tags($html);

    }
}