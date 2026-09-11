<?php
require_once __DIR__ . "/../Models/Clientes.php";
class ClientesController{
    public function getAll()
    {
        $cliente=CLIENTES::all();
        echo json_encode($cliente);
    }
    public function actualizar($id)
    {
        $data = $this->requestData();
        if ($data === null || !$this->requiredFields($data)) {
            return;
        }

        if (CLIENTES::update($id, $data)) {
            echo json_encode(["estado" => true, "message" => "cliente actualizado correctamente"]);
            return;
        }
        $this->error("No se pudo actualizar el cliente", 400);
    }

    public function add()
    {
        $data = $this->requestData();
        if ($data === null || !$this->requiredFields($data)) {
            return;
        }

        $id = CLIENTES::add($data);
        if ($id) {
            http_response_code(201);
            echo json_encode(["estado" => true, "message" => "cliente adicionado correctamente", "id" => $id]);
            return;
        }
        $this->error("No se pudo adicionar el cliente", 400);
    }

    public function eliminar($id)
    {
        if (CLIENTES::delete($id)) {
            echo json_encode(["estado" => true, "message" => "cliente eliminado correctamente"]);
            return;
        }
        $this->error("No se pudo eliminar el cliente", 400);
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
        foreach (['ci', 'nombre', 'apellidos', 'telefono'] as $field) {
            if (!array_key_exists($field, $data) || trim((string)$data[$field]) === '') {
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
