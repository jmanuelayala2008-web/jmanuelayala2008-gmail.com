<?php
include_once __DIR__. "/../config/conexionDB.php";

class Users {
    public static function findByUsername($username)
    {
        $rows = ConexionPDO::query(
            'SELECT u.id, u.username, u.password_hash, u.estado, u.cod_empleado, e.nombre AS employee_nombre, e.apellidos AS employee_apellidos FROM USUARIOS u LEFT JOIN EMPLEADOS e ON e.id = u.cod_empleado WHERE u.username = :username LIMIT 1',
            [':username' => $username]
        );
        return $rows[0] ?? null;
    }

    public static function all() {
        $sql="SELECT id, username, estado, cod_empleado FROM USUARIOS";
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
