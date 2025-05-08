<?php
class OfertaLaboral {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obtenerOfertasActivas() {
        try {
            $query = "SELECT * FROM OfertaLaboral WHERE estado = 'Vigente'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener ofertas: " . $e->getMessage());
            return [];
        }
    }

    public function crearOferta($data) {
        try {
            error_log("Datos recibidos para crearOferta: " . json_encode($data)); // Depuración
    
            $query = "INSERT INTO OfertaLaboral (titulo, descripcion, ubicacion, salario, tipo_contrato, fecha_cierre, reclutador_id)
                      VALUES (:titulo, :descripcion, :ubicacion, :salario, :tipo_contrato, :fecha_cierre, :reclutador_id)";
            $stmt = $this->conn->prepare($query);
    
            $stmt->bindParam(':titulo', $data['titulo']);
            $stmt->bindParam(':descripcion', $data['descripcion']);
            $stmt->bindParam(':ubicacion', $data['ubicacion']);
            $stmt->bindParam(':salario', $data['salario']);
            $stmt->bindParam(':tipo_contrato', $data['tipo_contrato']);
            $stmt->bindParam(':fecha_cierre', $data['fecha_cierre']);
            $stmt->bindParam(':reclutador_id', $data['reclutador_id']);
    
            $resultado = $stmt->execute();
            error_log("Resultado de la ejecución: " . ($resultado ? "Éxito" : "Fallo")); // Depuración
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error al crear oferta laboral: " . $e->getMessage());
            return false;
        }
    }

    public function desactivarOferta($id) {
        try {
            $query = "UPDATE OfertaLaboral SET estado = 'Baja' WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al desactivar oferta: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarOferta($id, $data) {
        try {
            error_log("Datos recibidos para actualizarOferta: ID=$id, " . json_encode($data)); // Depuración
    
            $query = "UPDATE OfertaLaboral SET 
                        titulo = COALESCE(:titulo, titulo),
                        descripcion = COALESCE(:descripcion, descripcion),
                        ubicacion = COALESCE(:ubicacion, ubicacion),
                        salario = COALESCE(:salario, salario),
                        tipo_contrato = COALESCE(:tipo_contrato, tipo_contrato),
                        fecha_cierre = COALESCE(:fecha_cierre, fecha_cierre),
                        estado = COALESCE(:estado, estado)
                      WHERE id = :id";
            $stmt = $this->conn->prepare($query);
    
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':titulo', $data['titulo']);
            $stmt->bindParam(':descripcion', $data['descripcion']);
            $stmt->bindParam(':ubicacion', $data['ubicacion']);
            $stmt->bindParam(':salario', $data['salario']);
            $stmt->bindParam(':tipo_contrato', $data['tipo_contrato']);
            $stmt->bindParam(':fecha_cierre', $data['fecha_cierre']);
            $stmt->bindParam(':estado', $data['estado']);
    
            $resultado = $stmt->execute();
            error_log("Resultado de la ejecución: " . ($resultado ? "Éxito" : "Fallo")); // Depuración
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error al actualizar oferta laboral: " . $e->getMessage());
            return false;
        }
    }

    public function eliminarOferta($id) {
        try {
            $query = "DELETE FROM OfertaLaboral WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar oferta laboral: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerPostulantesPorOferta($ofertaId) {
        try {
            $query = "SELECT p.id, p.nombre, p.email, p.estado_postulacion 
                      FROM Postulantes p
                      WHERE p.oferta_id = :oferta_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':oferta_id', $ofertaId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener postulantes: " . $e->getMessage());
            return [];
        }
    }

    public function actualizarEstadoPostulacion($postulacionId, $estado) {
        try {
            $query = "UPDATE Postulantes SET estado_postulacion = :estado WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $postulacionId);
            $stmt->bindParam(':estado', $estado);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar estado de postulación: " . $e->getMessage());
            return false;
        }
    }

}
?>