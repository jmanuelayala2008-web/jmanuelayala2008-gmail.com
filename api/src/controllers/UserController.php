<?php
require_once __DIR__ . "/../Models/Users.php";
class UserController{
    public function getAll()
    {
        $user=Users::all();
        echo json_encode($user);
         
    }

    public function add()
    {
        $data = $this->requestData();
        if ($data === null || !$this->requiredFields($data)) {
            return;
        }

        $id = Users::add($data);
        if ($id) {
            http_response_code(201);
            echo json_encode(["estado" => true, "message" => "usuario adicionado correctamente", "id" => $id]);
            return;
        }
        $this->error("No se pudo adicionar el usuario", 400);
    }

    public function actualizar($id)
    {
        $data = $this->requestData();
        if ($data === null || !$this->requiredFields($data)) {
            return;
        }

        if (Users::update($id, $data)) {
            echo json_encode(["estado" => true, "message" => "usuario actualizado correctamente"]);
            return;
        }
        $this->error("No se pudo actualizar el usuario", 400);
    }

    public function eliminar($id)
    {
        if (Users::delete($id)) {
            echo json_encode(["estado" => true, "message" => "usuario eliminado correctamente"]);
            return;
        }
        $this->error("No se pudo eliminar el usuario", 400);
    }

    private function requestData()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $this->error("El cuerpo de la solicitud debe ser JSON valido", 400);
            return null;
        }
        return $data;
    }

    private function requiredFields($data)
    {
        foreach (['username', 'password_hash', 'estado', 'cod_empleado'] as $field) {
            if (!array_key_exists($field, $data) || $data[$field] === '') {
                $this->error("El campo $field es obligatorio", 400);
                return false;
            }
        }
        return true;
    }

    private function error($message, $status)
    {
        http_response_code($status);
        echo json_encode(["estado" => false, "message" => $message]);
    }
}
