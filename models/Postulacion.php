<?php
class Postulacion {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function postular($data) {
        try {
            // Verificar si la oferta existe y está vigente
            $queryOferta = "SELECT id FROM OfertaLaboral WHERE id = :oferta_laboral_id AND estado = 'Vigente'";
            $stmtOferta = $this->conn->prepare($queryOferta);
            $stmtOferta->bindParam(':oferta_laboral_id', $data['id_oferta']); // Cambia ':id_oferta' a ':oferta_laboral_id'
            $stmtOferta->execute();
            if ($stmtOferta->rowCount() === 0) {
                error_log("Error al postular: La oferta no existe o no está vigente");
                return false;
            }
    
            // Verificar si el usuario existe y es un candidato
            $queryUsuario = "SELECT id FROM Usuario WHERE id = :id_usuario AND rol = 'Candidato'";
            $stmtUsuario = $this->conn->prepare($queryUsuario);
            $stmtUsuario->bindParam(':id_usuario', $data['id_usuario']);
            $stmtUsuario->execute();
            if ($stmtUsuario->rowCount() === 0) {
                error_log("Error al postular: El usuario no existe o no es un candidato");
                return false;
            }
    
            // Verificar si ya existe una postulación para esta oferta y usuario
            $queryDuplicado = "SELECT id FROM Postulacion WHERE oferta_laboral_id = :oferta_laboral_id AND candidato_id = :id_usuario";
            $stmtDuplicado = $this->conn->prepare($queryDuplicado);
            $stmtDuplicado->bindParam(':oferta_laboral_id', $data['id_oferta']); // Cambia ':id_oferta' a ':oferta_laboral_id'
            $stmtDuplicado->bindParam(':id_usuario', $data['id_usuario']);
            $stmtDuplicado->execute();
            if ($stmtDuplicado->rowCount() > 0) {
                error_log("Error al postular: El usuario ya se ha postulado a esta oferta");
                return false;
            }
    
            // Insertar la postulación
            $query = "INSERT INTO Postulacion (oferta_laboral_id, candidato_id, estado_postulacion, comentario, fecha_postulacion)
                      VALUES (:oferta_laboral_id, :id_usuario, 'Postulando', '', NOW())";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':oferta_laboral_id', $data['id_oferta']); // Cambia ':id_oferta' a ':oferta_laboral_id'
            $stmt->bindParam(':id_usuario', $data['id_usuario']);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al postular: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarEstado($id, $estado_postulacion, $comentario) {
        try {
            $query = "UPDATE Postulacion 
                      SET estado_postulacion = :estado_postulacion, 
                          comentario = CONCAT(IFNULL(comentario, ''), '\n', :comentario) 
                      WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':estado_postulacion', $estado_postulacion);
            $stmt->bindParam(':comentario', $comentario);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar estado de postulación: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerPostulacionesPorOferta($id_oferta) {
        $query = "SELECT * FROM Postulacion WHERE id_oferta = :id_oferta";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_oferta', $id_oferta);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPostulacionesPorCandidato($id_usuario) {
        try {
            $query = "SELECT p.id, p.estado_postulacion, p.comentario, p.fecha_postulacion, p.fecha_actualizacion, 
                             o.titulo AS oferta_titulo, o.descripcion AS oferta_descripcion
                      FROM Postulacion p
                      INNER JOIN OfertaLaboral o ON p.oferta_laboral_id = o.id
                      WHERE p.candidato_id = :id_usuario";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_usuario', $id_usuario);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener postulaciones por candidato: " . $e->getMessage());
            return [];
        }
    }

    public function crearPostulacion($data) {
        try {
            $query = "INSERT INTO Postulacion (candidato_id, oferta_laboral_id, estado_postulacion, comentario)
                      VALUES (:candidato_id, :oferta_laboral_id, :estado_postulacion, :comentario)";
            $stmt = $this->conn->prepare($query);
    
            $stmt->bindParam(':candidato_id', $data['candidato_id']);
            $stmt->bindParam(':oferta_laboral_id', $data['oferta_laboral_id']);
            $stmt->bindParam(':estado_postulacion', $data['estado_postulacion']);
            $stmt->bindParam(':comentario', $data['comentario']);
    
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al crear postulación: " . $e->getMessage());
            return false;
        }
    }

    public function listarPostulantesPorOferta($oferta_laboral_id) {
        try {
            $query = "SELECT u.id AS candidato_id, u.nombre, u.apellido, u.email, 
                             p.estado_postulacion, p.comentario, p.fecha_postulacion
                      FROM Postulacion p
                      INNER JOIN Usuario u ON p.candidato_id = u.id
                      WHERE p.oferta_laboral_id = :oferta_laboral_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':oferta_laboral_id', $oferta_laboral_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al listar postulantes por oferta: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerPostulacionPorId($id) {
        try {
            $query = "SELECT p.id, p.candidato_id, p.oferta_laboral_id, p.estado_postulacion, p.comentario, p.fecha_postulacion, p.fecha_actualizacion,
                             u.nombre AS candidato_nombre, u.apellido AS candidato_apellido, o.titulo AS oferta_titulo
                      FROM Postulacion p
                      INNER JOIN Usuario u ON p.candidato_id = u.id
                      INNER JOIN OfertaLaboral o ON p.oferta_laboral_id = o.id
                      WHERE p.id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener postulación: " . $e->getMessage());
            return null;
        }
    }
}
?>