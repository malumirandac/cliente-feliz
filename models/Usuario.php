<?php

class Usuario {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function insertarUsuario($data) {
        try {
            error_log("Datos recibidos en insertarUsuario: " . json_encode($data)); // Depuración
    
            // Cambiar el alias del campo 'contraseña' a 'password' en la consulta
            $query = "INSERT INTO Usuario (nombre, apellido, email, contraseña, fecha_nacimiento, telefono, direccion, rol, estado)
                      VALUES (:nombre, :apellido, :email, :password, :fecha_nacimiento, :telefono, :direccion, :rol, :estado)";
            $stmt = $this->conn->prepare($query);
    
            // Vincular parámetros
            $stmt->bindParam(':nombre', $data['nombre']);
            $stmt->bindParam(':apellido', $data['apellido']);
            $stmt->bindParam(':email', $data['email']);
            $passwordHash = password_hash($data['contraseña'], PASSWORD_DEFAULT); // Hashear la contraseña
            $stmt->bindParam(':password', $passwordHash); // Cambiar a ':password'
            $stmt->bindParam(':fecha_nacimiento', $data['fecha_nacimiento']);
            $stmt->bindParam(':telefono', $data['telefono']);
            $stmt->bindParam(':direccion', $data['direccion']);
            $stmt->bindParam(':rol', $data['rol']);
            $stmt->bindParam(':estado', $data['estado']);
    
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al insertar usuario: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerUsuarios() {
        try {
            $query = "SELECT * FROM Usuario";
            $stmt = $this->conn->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener usuarios: " . $e->getMessage());
            return [];
        }
    }

    public function actualizarUsuario($id, $data) {
        try {
            error_log("Datos recibidos para actualizarUsuario: ID=$id, " . json_encode($data)); // Depuración
    
            $query = "UPDATE Usuario SET 
                        nombre = COALESCE(:nombre, nombre),
                        apellido = COALESCE(:apellido, apellido),
                        email = COALESCE(:email, email),
                        contraseña = COALESCE(:password, contraseña),
                        direccion = COALESCE(:direccion, direccion),
                        rol = COALESCE(:rol, rol),
                        estado = COALESCE(:estado, estado)
                      WHERE id = :id";
            $stmt = $this->conn->prepare($query);
    
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nombre', $data['nombre']);
            $stmt->bindParam(':apellido', $data['apellido']);
            $stmt->bindParam(':email', $data['email']);
            
            // Manejar correctamente el valor de contraseña
            $passwordHash = !empty($data['contraseña']) ? password_hash($data['contraseña'], PASSWORD_DEFAULT) : null;
            $stmt->bindParam(':password', $passwordHash);
    
            $stmt->bindParam(':direccion', $data['direccion']);
            $stmt->bindParam(':rol', $data['rol']);
            $stmt->bindParam(':estado', $data['estado']);
    
            $resultado = $stmt->execute();
            error_log("Resultado de la ejecución: " . ($resultado ? "Éxito" : "Fallo")); // Depuración
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error al actualizar usuario: " . $e->getMessage());
            return false;
        }
    }

    public function eliminarUsuario($id) {
        try {
            $query = "DELETE FROM Usuario WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar usuario: " . $e->getMessage());
            return false;
        }
    }

}
?>