<?php


namespace App\Utility;


class MarkDownParser
{
    function parserToHtml(string $path)
    {
        $content = file_get_contents($path);
    }
}