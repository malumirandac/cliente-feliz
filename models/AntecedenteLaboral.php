<?php
class AntecedenteLaboral {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obtenerPorCandidato($id) {
        $query = "SELECT * FROM AntecedenteLaboral WHERE candidato_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertar($data) {
        try {
            $query = "INSERT INTO AntecedenteLaboral (candidato_id, empresa, cargo, funciones, fecha_inicio, fecha_termino)
                      VALUES (:candidato_id, :empresa, :cargo, :funciones, :fecha_inicio, :fecha_termino)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':candidato_id', $data['candidato_id']);
            $stmt->bindParam(':empresa', $data['empresa']);
            $stmt->bindParam(':cargo', $data['cargo']);
            $stmt->bindParam(':funciones', $data['funciones']);
            $stmt->bindParam(':fecha_inicio', $data['fecha_inicio']);
            $stmt->bindParam(':fecha_termino', $data['fecha_termino']);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al insertar antecedente laboral: " . $e->getMessage());
            return false;
        }
    }
}
?>
