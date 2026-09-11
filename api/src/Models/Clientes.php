<?php
include_once __DIR__. "/../config/conexionDB.php";
class CLIENTES{

    public static function all() {
        $sql="SELECT * FROM CLIENTES";
       return conexionPDO::query($sql);
    }

    public static function add($data) {
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
        return conexionPDO::execute(
            "INSERT INTO CLIENTES (" . implode(',', $campos) . ") VALUES (" . implode(',', $parametros) . ")",
            $valores,
            true
        );
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
        return conexionPDO::execute(
            "UPDATE CLIENTES SET " . implode(',', $campos) . " WHERE id=:id",
            $valores,
            false
        );
    }

    public static function delete($id) {
        return conexionPDO::execute(
            "DELETE FROM CLIENTES WHERE id=:id",
            [':id' => $id],
            false
        );
    }

    private static function allowedData($data) {
        if (!is_array($data)) {
            return [];
        }
        return array_intersect_key($data, array_flip([
            'ci', 'nombre', 'apellidos', 'direccion', 'telefono'
        ]));
    }
}
