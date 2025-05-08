<?php
// controllers/UsuarioController.php
require_once './models/Usuario.php';

class UsuarioController {
    private $usuario;

    public function __construct($db) {
        $this->usuario = new Usuario($db);
    }

    public function insertarUsuario($data) {
        //validar que los campos not null no esten vacios
        if (!empty($data['nombre']) && !empty($data['apellido']) && !empty($data['email']) && !empty($data['contraseña']) && !empty($data['rol'])) {
            $resultado = $this->usuario->insertarUsuario($data);
            if (is_array($resultado) && isset($resultado['error'])) {
                http_response_code(500);
                echo json_encode(["mensaje" => "Error al insertar usuario", "detalle" => $resultado['error']]);
            } elseif ($resultado) {
                http_response_code(201);
                echo json_encode(["mensaje" => "Usuario insertado correctamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["mensaje" => "No se pudo insertar el usuario"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["mensaje" => "Faltan campos requeridos"]);
        }
    }

    public function obtenerUsuarios() {
        try {
            // Llama al método obtenerUsuarios del modelo Usuario
            $usuarios = $this->usuario->obtenerUsuarios();
            if (!empty($usuarios)) {
                http_response_code(200);
                echo json_encode($usuarios);
            } else {
                http_response_code(404);
                echo json_encode(["mensaje" => "No se encontraron usuarios"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al obtener usuarios", "detalle" => $e->getMessage()]);
        }
    }

    public function actualizarUsuario($id, $data) {
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID de usuario no proporcionado"]);
            return;
        }
    
        if (!empty($data['nombre']) || !empty($data['apellido']) || !empty($data['email']) || !empty($data['contraseña']) || !empty($data['rol'])) {
            $resultado = $this->usuario->actualizarUsuario($id, $data);
            if ($resultado) {
                http_response_code(200);
                echo json_encode(["mensaje" => "Usuario actualizado correctamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["mensaje" => "No se pudo actualizar el usuario"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["mensaje" => "Faltan campos requeridos para actualizar"]);
        }
    }

    public function actualizarParcialUsuario($id, $data) {
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID de usuario no proporcionado"]);
            return;
        }
    
        if (!empty($data)) {
            $resultado = $this->usuario->actualizarUsuario($id, $data);
            if ($resultado) {
                http_response_code(200);
                echo json_encode(["mensaje" => "Usuario actualizado parcialmente"]);
            } else {
                http_response_code(500);
                echo json_encode(["mensaje" => "No se pudo actualizar el usuario"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["mensaje" => "No se proporcionaron datos para actualizar"]);
        }
    }

    public function eliminarUsuario($id) {
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID de usuario no proporcionado"]);
            return;
        }
    
        $resultado = $this->usuario->eliminarUsuario($id);
        if ($resultado) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Usuario eliminado correctamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "No se pudo eliminar el usuario"]);
        }
    }


}
