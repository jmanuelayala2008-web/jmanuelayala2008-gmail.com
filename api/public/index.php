<?php
include_once __DIR__. "/../config/conexionDB.php";
class clientes{

    public static function all() {
        $sql="SELECT * FROM CLIENTES";
        return ConexionPDO::query($sql);//self::$users;
    }
}