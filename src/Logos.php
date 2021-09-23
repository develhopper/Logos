<?php

namespace develhopper\Logos;

/**
 * The Logos Class
 * 
 * This class is a simple kernel used for comiunicate with a web based shell program 
 * 
 * @author Develhopper <alireza.tjd77@gmail.com>
 */
class Logos{

    /**
     * Singleton Instance
     *
     * @var Logos
     */
    private static $instance = null;

    /**
     * Array of registered commands
     *
     * @var array
     */
    private $commands = [];

    /**
     * This is a Clousre pointer and will execute before run method
     * 
     * @var callable
     */
    private $beforeRun = null;
    
    /**
     * This is a Clousre pointer and will execute after run method
     * 
     * @var callable
     */
    private $afterRun = null;

    private function __construct(){}

    public static function getInstance(){
        if(self::$instance == null){
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * Register new command
     *
     * @param string $regex
     * @param callable $closure
     * @return void
     */
    public function register(string $regex, callable $closure) : void {
        $regex = preg_replace('/\{.*?\}/', '(.+)', $regex);
        array_push($this->commands, [
            'regex' => "/^$regex\$/",
            'closure' => $closure
        ]);
    }

    /**
     * Register beforeRun callable
     *
     * @param callable $closure
     * @return void
     */
    public function beforeRun(callable $closure) : void{
        $this->beforeRun = $closure;
    }

    /**
     * Register afterRun callable
     *
     * @param callable $closure
     * @return void
     */
    public function afterRun(callable $closure){
        $this->afterRun = $closure;
    }

    /**
     * if command string matches any registered commands then will execute it closure
     * otherwise it will return -1
     * 
     * this method will run beforeRun closure before running
     * and will run afterRun closure and pass result of command to it for modification
     *  
     * @param string $command
     * @return mixed
     */
    public function run(string $command) : mixed {
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

    /**
     * Call closures and callables
     *
     * @param callable $closure
     * @param array $params
     * @return mixed
     */
    public function call_closure(callable $closure, array $params = []) : mixed{
        if(is_array($closure))
            return call_user_func_array($closure, $params);
        else
            return call_user_func($closure, $params);
    }
    
}