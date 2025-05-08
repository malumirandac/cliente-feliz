<?php
require_once './models/OfertaLaboral.php';
require_once './models/Postulacion.php';

class CandidatoController {
    private $ofertaModel;
    private $postulacion;

    public function __construct($db) {
        $this->ofertaModel = new OfertaLaboral($db);
        $this->postulacionModel = new Postulacion($db);
    }

    public function verOfertasActivas() {
        $ofertas = $this->ofertaLaboral->obtenerOfertasActivas();
        echo json_encode($ofertas);
    }

    public function postular($data) {
        if ($this->postulacion->postular($data)) {
            echo json_encode(["mensaje" => "Postulación realizada correctamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al realizar la postulación"]);
        }
    }

    public function verPostulaciones($id_usuario) {
        $postulaciones = $this->postulacion->obtenerPostulacionesPorUsuario($id_usuario);
        echo json_encode($postulaciones);
    }
}

?>