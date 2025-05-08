<?php
class AntecedenteAcademico {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obtenerPorCandidato($id) {
        $query = "SELECT * FROM AntecedenteAcademico WHERE candidato_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertar($data) {
        try {
            $query = "INSERT INTO AntecedenteAcademico (candidato_id, institucion, titulo_obtenido, anio_ingreso, anio_egreso)
                      VALUES (:candidato_id, :institucion, :titulo_obtenido, :anio_ingreso, :anio_egreso)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':candidato_id', $data['candidato_id']);
            $stmt->bindParam(':institucion', $data['institucion']);
            $stmt->bindParam(':titulo_obtenido', $data['titulo_obtenido']);
            $stmt->bindParam(':anio_ingreso', $data['anio_ingreso']);
            $stmt->bindParam(':anio_egreso', $data['anio_egreso']);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al insertar antecedente académico: " . $e->getMessage());
            return false;
        }
    }
}
?>