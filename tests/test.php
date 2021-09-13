<?php

require __DIR__."/../vendor/autoload.php";

use develhopper\Logos\Logos;

$kernel = Logos::getInstance();

$kernel->register('cd {dir}', function($input){
    var_dump($input);
});

$kernel->run('cd test');