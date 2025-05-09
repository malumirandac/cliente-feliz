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
            $query = "SELECT p.id AS postulacion_id, 
                             u.id AS candidato_id, 
                             u.nombre, 
                             u.apellido, 
                             u.email, 
                             p.estado_postulacion, 
                             p.comentario, 
                             p.fecha_postulacion
                      FROM Postulacion p
                      INNER JOIN Usuario u ON p.candidato_id = u.id
                      WHERE p.oferta_laboral_id = :ofertaId";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':ofertaId', $ofertaId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener postulantes: " . $e->getMessage());
            return [];
        }
    }

    public function actualizarEstadoPostulacion($id, $estado) {
        try {
            $query = "UPDATE Postulacion 
                      SET estado_postulacion = :estado, 
                          fecha_actualizacion = NOW() 
                      WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':estado', $estado);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar estado de postulación: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerPostulacionPorId($id) {
        try {
            $query = "SELECT p.id AS postulacion_id, 
                             p.candidato_id, 
                             p.oferta_laboral_id, 
                             p.estado_postulacion, 
                             p.comentario, 
                             p.fecha_postulacion, 
                             p.fecha_actualizacion,
                             u.nombre AS candidato_nombre, 
                             u.apellido AS candidato_apellido, 
                             o.titulo AS oferta_titulo
                      FROM Postulacion p
                      LEFT JOIN Usuario u ON p.candidato_id = u.id
                      LEFT JOIN OfertaLaboral o ON p.oferta_laboral_id = o.id
                      WHERE p.id = :id";
    
            error_log("Consulta SQL ejecutada: " . $query);
            error_log("ID recibido en el modelo: " . $id);
    
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener postulación: " . $e->getMessage());
            return null;
        }
    }

}
?>