<?php

namespace develhopper\Logos;

class Logos{

    private static $instance = null;

    private $commands = [];

    private $beforeRun = null;
    private $afterRun = null;

    private function __construct(){}

    public static function getInstance(){
        if(self::$instance == null){
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function register($regex, $closure){
        $regex = preg_replace('/\{.*?\}/', '(.+)', $regex);
        array_push($this->commands, [
            'regex' => "/^$regex\$/",
            'closure' => $closure
        ]);
    }

    public function beforeRun($closure){
        $this->beforeRun = $closure;
    }

    public function afterRun($closure){
        $this->afterRun = $closure;
    }

    public function run($command){
        foreach($this->commands as $item){
            preg_match($item['regex'], $command, $matches);
            if($matches){    

                if($this->beforeRun){
                    $this->call_closure($this->beforeRun,[$item['closure']]);
                }
                
                $result = $this->call_closure($item['closure'], array_slice($matches, 1));
                
                if($this->afterRun){
                    return $this->call_closure($this->afterRun, [$result]);
                }

                return $result;
            }
        }
        return -1;
    }

    public function call_closure($closure, $params = []){
        if(is_array($closure))
            return call_user_func_array($closure, $params);
        else
            return call_user_func($closure, $params);
    }
    
}