<?php


namespace App\Utility\Markdown;


class Parser
{
    public static function parserToHtml(string $path):ParserResult
    {
        $result = new ParserResult();
        $content = '';
        $head = '';

        $file = fopen($path, "r");
        $isInHead = false;
        while(!feof($file))
        {
            $line = fgets($file);
            if($isInHead){
                if(substr($line,0,3) == '---'){
                    $isInHead = false;
                }else{
                    $head = $head.$line;
                }
            }else{
                if(substr($line,0,3) == '---'){
                    $isInHead = true;
                }else{
                    $content = $content.$line;
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
}