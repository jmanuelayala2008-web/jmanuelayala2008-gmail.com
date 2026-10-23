<?php
require_once __DIR__ . "/../Models/Users.php";
class UserController{
    public function login()
    {
        $data = $this->requestData();
        if ($data === null || !isset($data['username'], $data['password'])
            || !is_string($data['username']) || !is_string($data['password'])
            || $data['username'] === '' || $data['password'] === '') {
            $this->error('Se requieren usuario y contrasena', 400);
            return;
        }

        $user = Users::findByUsername($data['username']);
        if (!$user || (int)$user['estado'] !== 1 || !$this->passwordMatches($data['password'], $user['password_hash'])) {
            $this->error('Usuario o contrasena incorrectos', 401);
            return;
        }

        session_regenerate_id(true);
        $employeeName = trim(($user['employee_nombre'] ?? '') . ' ' . ($user['employee_apellidos'] ?? ''));
        $_SESSION['authenticated_user'] = [
            'id' => (int)$user['id'],
            'username' => $user['username'],
            'displayName' => $employeeName !== '' ? $employeeName : $user['username'],
            'estado' => (int)$user['estado'],
            'cod_empleado' => $user['cod_empleado'] === null ? null : (int)$user['cod_empleado'],
        ];
        echo json_encode([
            'authenticated' => true,
            'sessionId' => session_id(),
            'user' => $_SESSION['authenticated_user'],
        ], JSON_UNESCAPED_UNICODE);
    }

    public function session()
    {
        if (empty($_SESSION['authenticated_user'])) {
            $this->error('No hay una sesion activa', 401);
            return;
        }

        echo json_encode([
            'authenticated' => true,
            'user' => $_SESSION['authenticated_user'],
        ], JSON_UNESCAPED_UNICODE);
    }

    public function logout()
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE && !session_destroy()) {
            $this->error('No se pudo cerrar la sesion', 500);
            return;
        }
        echo json_encode(['authenticated' => false, 'message' => 'Sesion cerrada']);
    }

    private function passwordMatches($password, $storedPassword)
    {
        if (password_verify($password, $storedPassword)) return true;
        $passwordInfo = password_get_info($storedPassword);
        if (empty($passwordInfo['algo'])) return hash_equals($storedPassword, $password);
        return false;
    }

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

        $data['password_hash'] = password_hash($data['password_hash'], PASSWORD_DEFAULT);
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
