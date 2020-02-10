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
        $i=0;
        while (is_resource($file) && !feof($file)) {
            $line = fgets($file);
            if ($isInHead) {
                if (strlen(trim($line))==3&&substr($line, 0, 3) == '---') {
                    $isInHead = false;
                } else {
                    $head = $head . $line;
                }
            } else {
                if (!empty($head)){
                    continue;
                }
                if (strlen(trim($line))==3&&substr($line, 0, 3) == '---') {
                    $isInHead = true;
                } else {
                    $content = $content . $line;
                }
            }
            $i++;
        }
        fclose($file);
        $result->setConfig(yaml_parse($head));
        $parsedown = new \Parsedown();
        $html = $parsedown->text($content);
        $result->setHtml($html);
        return $result;
    }

    public static function getHtml($path)
    {
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
        //处理链接类标签
        $aList = $dom->find('a');
        /**
         * @var $a SimpleHtmlDom
         */
        foreach ($aList as $a) {
            $info = pathinfo($a->href);
            if (isset($info['extension']) && ($info['extension'] == 'md')) {
                $a->href = self:: changeLink($a->href);
            } else {
                $a->setAttribute("target", "_blank");
            }
        }

        //处理h类标签
        $hList = $dom->find('h1,h2,h3,h4,h5,h6');
        foreach ($hList as $h) {
            $h->setAttribute('id',$h->getNode()->textContent);
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