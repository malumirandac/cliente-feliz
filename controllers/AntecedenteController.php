<?php
require_once './models/AntecedenteAcademico.php';
require_once './models/AntecedenteLaboral.php';

class AntecedenteController {
    private $academico;
    private $laboral;

    public function __construct($db) {
        $this->academico = new AntecedenteAcademico($db);
        $this->laboral = new AntecedenteLaboral($db);
    }

    public function obtenerAcademicos($id) {
        $resultado = $this->academico->obtenerPorCandidato($id);
        echo json_encode(["data" => $resultado]);
    }

    public function insertarAcademico($data) {
        $resultado = $this->academico->insertar($data);
        echo json_encode(["mensaje" => $resultado ? "Registro académico guardado" : "Error al guardar registro académico"]);
    }

    public function obtenerLaborales($id) {
        $resultado = $this->laboral->obtenerPorCandidato($id);
        echo json_encode(["data" => $resultado]);
    }

    public function insertarLaboral($data) {
        $resultado = $this->laboral->insertar($data);
        echo json_encode(["mensaje" => $resultado ? "Registro laboral guardado" : "Error al guardar registro laboral"]);
    }
}