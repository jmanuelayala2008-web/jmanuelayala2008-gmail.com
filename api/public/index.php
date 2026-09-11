<?php
if($_SERVER['REQUEST_METHOD']=='OPTIONS')
    {
        exit;
    }
require_once "../src/Router.php";
require_once "../src/Controllers/UserController.php";
require_once "../src/Controllers/ProductoController.php";
require_once "../src/Controllers/ClientesController.php";

use App\Router;

$route=new Router();
//direccion para usuario
$route->add('GET','/','UserController@getAll');
$route->add('GET','/users','UserController@getAll');
$route->add('POST','/users','UserController@add');
$route->add('PUT','/users/{id}','UserController@actualizar');
$route->add('DELETE','/users/{id}','UserController@eliminar');
//direccion de producto
$route->add('GET','/productos','ProductoController@getAll');
$route->add('PUT','/productos/{id}','ProductoController@actualizar');
$route->add('POST','/productos','ProductoController@add');
$route->add('DELETE','/productos/{id}','ProductoController@eliminar');
//clientes
$route->add('GET','/CLIENTES','ClientesController@getAll');
$route->add('POST','/CLIENTES','ClientesController@add');
$route->add('PUT','/CLIENTES/{id}','ClientesController@actualizar');
$route->add('DELETE','/CLIENTES/{id}','ClientesController@eliminar');





$route->run();
