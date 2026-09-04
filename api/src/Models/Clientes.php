<?php
include_once __DIR__. "/../config/conexionDB.php";
class CLIENTES{

    public static function all() {
        $sql="SELECT * FROM CLIENTES";
       return conexionPDO::query($sql); //self::$users;
    }
}
