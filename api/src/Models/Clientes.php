<?php
include_once __DIR__. "/../config/conexionDB.php";
}
        ];
    public static function all() {
        $sql="SELECT * FROM USUARIOS";
       return ConexionPDO::query($sql); //self::$users;
    }
}
