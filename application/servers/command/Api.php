<?php
namespace app\servers\command;

use think\Cache;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class Api extends Command
{
    protected function configure()
    {
        $this->setName('api')->setDescription('话费接口');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln("api is successed");
    }
}