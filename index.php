<?php
define('CONFIG_PATH',__DIR__.'/config/config.php');
spl_autoload_register(function ($class_name) {
    $class_name = explode('\\',$class_name);
    $class_name = implode('/',$class_name);
    require_once $class_name . '.php';
});

    $redisConfig = \classes\Config::get('REDIS_SERVER');
    $redis = new Redis();
    if(!empty($redisConfig['host'])){
        $redis->connect($redisConfig['host'],$redisConfig['port']);
        $redis->auth($redisConfig['password']);
    }

    //analyze request
    isset($_SERVER['PATH_INFO'])?$documentPath = $_SERVER['PATH_INFO']:$documentPath = '/Index/index';

    $documentPath = explode('/',$documentPath);

    list($module, $className, $methodName) = $documentPath;
    $className = 'classes\\'.$className;

    //reflection to invoke the method
    try{
        $reflectionMethod = new ReflectionMethod($className, $methodName);
        $reflectionMethod->isPublic()?$is_public = true:$is_public = false;
        if(!$is_public){
            $data = [
                'code'      =>  910,
                'message'   => 'method is not public',
                'data'      =>  ''
            ];
            echo json_encode($data);exit;
        }

        $class = new $className($redis);
        $redata = $reflectionMethod->invoke($class);

        echo json_encode($redata);

    }catch (Exception $e){
        $data = [
            'code'      => 900,
            'message'   => $e->getMessage(),
            'data'      =>  ''
        ];
        echo json_encode($data);
    }

