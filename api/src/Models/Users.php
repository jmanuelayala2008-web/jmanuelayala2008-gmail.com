<?php
include_once __DIR__. "/../config/conexionDB.php";

class Users {
    public static function all() {
        $sql="SELECT * FROM USUARIOS";
       return ConexionPDO::query($sql);
    }

    public static function add($data) {
        return self::save($data);
    }

    public static function update($id, $data) {
        unset($data['id']);
        $data = self::allowedData($data);
        if (empty($data)) {
            return false;
        }

        $campos = [];
        $valores = [':id' => $id];
        foreach ($data as $columna => $valor) {
            $campos[] = "$columna=:$columna";
            $valores[":$columna"] = $valor;
        }
        return ConexionPDO::execute(
            "UPDATE USUARIOS SET " . implode(',', $campos) . " WHERE id=:id",
            $valores,
            false
        );
    }

    public static function delete($id) {
        return ConexionPDO::execute(
            "DELETE FROM USUARIOS WHERE id=:id",
            [':id' => $id],
            false
        );
    }

    private static function save($data) {
        $data = self::allowedData($data);
        if (empty($data)) {
            return false;
        }

        $campos = array_keys($data);
        $parametros = array_map(fn($campo) => ":$campo", $campos);
        $valores = [];
        foreach ($data as $columna => $valor) {
            $valores[":$columna"] = $valor;
        }
        return ConexionPDO::execute(
            "INSERT INTO USUARIOS (" . implode(',', $campos) . ") VALUES (" . implode(',', $parametros) . ")",
            $valores,
            true
        );
    }

    private static function allowedData($data) {
        if (!is_array($data)) {
            return [];
        }
        return array_intersect_key($data, array_flip([
            'username', 'email', 'password_hash', 'estado', 'cod_empleado'
        ]));
    }
}
