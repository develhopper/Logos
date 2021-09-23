<?php

require __DIR__."/../vendor/autoload.php";

use develhopper\Logos\Logos;

$kernel = Logos::getInstance();

$kernel->register('cd {dir}', function($input){
    return ['pwd' => $input];
});

$kernel->beforeRun(function($command){
    // Run before runing command
    var_dump($command);
});

$kernel->afterRun(function($result){
    // Run after running command for modification of results

    array_push($result, 'new element');

    return $result;
});

$result = $kernel->run('cd test');

var_dump($result);