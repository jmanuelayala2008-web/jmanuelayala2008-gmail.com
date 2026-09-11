<?php
$path=dirname(__DIR__,2).'/.env';
if(!file_exists($path)){
    die("Archivo de configuracion no encontrado");
}
$config=parse_ini_file($path);
define("HOST",$config['DB_HOST']);
define("DATABASE",$config['DB_NAME']);
define("USERNAME",$config['DB_USER']);
define("PASSWORD",$config['DB_PASSWORD']);
define("PORT",$config['DB_PORT']);
define("CHARSET",$config['DB_CHARSET']);
