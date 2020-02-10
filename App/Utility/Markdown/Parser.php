<?php


namespace App\Utility\Markdown;


use voku\helper\HtmlDomParser;
use voku\helper\SimpleHtmlDom;

class Parser
{
    public static function parserToHtml(string $path): ParserResult
    {
        $result = new ParserResult();
        $content = '';
        $head = '';

        $file = fopen($path, "r");
        $isInHead = false;
        while (is_resource($file) && !feof($file)) {
            $line = fgets($file);
            if ($isInHead) {
                if (substr($line, 0, 3) == '---') {
                    $isInHead = false;
                } else {
                    $head = $head . $line;
                }
            } else {
                if (substr($line, 0, 3) == '---') {
                    $isInHead = true;
                } else {
                    $content = $content . $line;
                }
            }
        }
        fclose($file);
        $result->setConfig(yaml_parse($head));
        $parsedown = new \Parsedown();
        $html = $parsedown->text($content);
        $result->setHtml($html);
        return $result;
    }

    public static function getHtml($path){
        $result = self::parserToHtml($path);
        $result->setHtml(self::handelHtml($result->getHtml()));
        return $result;
    }

    /**
     * 额外处理html内容
     * handelHtml
     * @param $html
     * @return mixed
     * @author tioncico
     * Time: 下午2:55
     */
    static function handelHtml($html)
    {
        $dom = HtmlDomParser::str_get_html($html);
        $aList = $dom->find('a');
        /**
         * @var $a SimpleHtmlDom
         */
        foreach ($aList as $a) {
            $a->href = self:: changeLink($a->href);
            $info = pathinfo($a->href);
            if (isset($info['extension']) && ($info['extension'] == 'md')) {
                $a->href = self:: changeLink($a->href);
            } else {
                $a->setAttribute("target", "_blank");
            }
        }

        return $dom->html();
    }

    static function changeLink($link)
    {
        if (substr($link, -3) == '.md') {
            return substr($link, 0, -3) . '.html';
        }
        return $link;
    }

}