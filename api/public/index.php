<?php
if($_SERVER['REQUEST_METHOD']=='OPTIONS')
    {
        exit;
    }
require_once __DIR__ . "/../src/Router.php";
require_once __DIR__ . "/../src/controllers/UserController.php";
require_once __DIR__ . "/../src/controllers/ProductoController.php";

use App\Router;

$route=new Router();
//direccion para usuario
$route->add('GET','/','UserController@getAll');
$route->add('GET','/users','UserController@getAll');
//direccion de producto
$route->add('GET','/productos','ProductoController@getAll');
$route->add('PUT','/productos/{id}','ProductoController@actualizar');
$route->add('POST','/productos','ProductoController@add');
$route->add('DELETE','/productos/{id}','ProductoController@eliminar');
$route->run();
