<?php
class Postulacion {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function postular($data) {
        try {
            $query = "INSERT INTO Postulacion (id_oferta, id_usuario, estado, comentario, fecha_postulacion)
                      VALUES (:id_oferta, :id_usuario, 'Postulando', '', NOW())";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_oferta', $data['id_oferta']);
            $stmt->bindParam(':id_usuario', $data['id_usuario']);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al postular: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarEstado($id, $estado, $comentario) {
        try {
            $query = "UPDATE Postulacion SET estado_postulacion = :estado, comentario = :comentario WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':estado', $estado);
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

    public function obtenerPostulacionesPorUsuario($id_usuario) {
        $query = "SELECT * FROM Postulacion WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
}
?>