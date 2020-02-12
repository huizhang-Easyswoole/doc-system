<?php


namespace App\Utility;


use EasySwoole\Component\Process\AbstractProcess;

class GitSyncProcess extends AbstractProcess
{

    protected function run($arg)
    {
        go(function (){
            while (1){
                /*
                 * 这边需要执行  git pull
                 */
                DocMdSearchParser::scan();
                \co::sleep(10);
            }
        });
    }
}