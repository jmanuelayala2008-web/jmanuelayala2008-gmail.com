<?php

namespace App;
class Router{
    protected $routes=[];
    public function add($method,$route,$handler)
    {
        $this->routes[]=[
            'method'=>$method,
            'route'=>$route,
            'handler'=>$handler,
        ];
       
    }
    public function run()
    {
        $uri=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
        // Funciona tanto si api/public es el document root como si se publica
        // en una subruta, por ejemplo /api/public/users.
        $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
        if ($scriptDir !== '' && $scriptDir !== '.' && strpos($uri, $scriptDir . '/') === 0) {
            $uri = substr($uri, strlen($scriptDir));
        }
        $method=$_SERVER['REQUEST_METHOD'];
         
        foreach($this->routes as $route)
            {
                $pattern=str_replace(['{id}','/'],['([0-9]+)','\/'],$route['route']);
                $pattern='/^'.$pattern.'$/';
                if($method===$route['method'] && preg_match($pattern,$uri,$matches))
                    { array_shift($matches);
                        list($controllerName,$methodName)=explode('@',$route['handler']);
                        $controller=new $controllerName();
                        return call_user_func_array([$controller,$methodName],$matches);
                    }
            }
            //si no encontro
            http_response_code(404);
            echo json_encode(["error"=>'Ruta no encontrada']);
    } 
    
}
