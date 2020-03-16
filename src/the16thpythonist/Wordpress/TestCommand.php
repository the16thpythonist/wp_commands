<?php


namespace the16thpythonist\Wordpress;


use the16thpythonist\Command\Command;

class TestCommand extends Command
{
    public $params = array();

    protected function run($args){
        $this->log("test");
    }
}