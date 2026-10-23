<?php
header('Content-Type: application/json; charset=utf-8');
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin === 'http://www.frontend') {
    header('Access-Control-Allow-Origin: http://www.frontend');
    header('Vary: Origin');
}
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Accept, Content-Type, X-Session-Id');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// The frontend and API use different virtual hosts. Carry the PHP session ID
// in a request header instead of relying on a cross-site browser cookie.
ini_set('session.use_cookies', '0');
ini_set('session.use_only_cookies', '0');
ini_set('session.use_strict_mode', '1');
ini_set('session.use_trans_sid', '0');
$sessionId = $_SERVER['HTTP_X_SESSION_ID'] ?? '';
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$isSessionRoute = preg_match('~/(login|session|logout)/?$~i', $requestPath) === 1;
if ($sessionId !== '' || $isSessionRoute) {
    if ($sessionId !== '' && !preg_match('/^[A-Za-z0-9,-]{16,128}$/', $sessionId)) {
        http_response_code(401);
        echo json_encode(['authenticated' => false, 'message' => 'Sesion no valida']);
        exit;
    }
    if ($sessionId !== '') session_id($sessionId);
    session_start();
}

require_once __DIR__ . "/../src/Router.php";
require_once __DIR__ . "/../src/Controllers/UserController.php";
require_once __DIR__ . "/../src/Controllers/ProductoController.php";
require_once __DIR__ . "/../src/Controllers/ClientesController.php";

use App\Router;

$route=new Router();
//direccion para usuario
$route->add('POST','/login','UserController@login');
$route->add('GET','/session','UserController@session');
$route->add('POST','/logout','UserController@logout');
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

try {
    $route->run();
} catch (Throwable $error) {
    error_log("Error en API SISTEMAMENU: ".$error->getMessage());
    http_response_code(500);
    echo json_encode(["error" => "Error interno al consultar la API"]);
}
