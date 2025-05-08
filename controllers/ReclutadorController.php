<?php
require_once './models/OfertaLaboral.php';
require_once './models/Postulacion.php';

class ReclutadorController {
    private $ofertaLaboral;
    private $postulacionModel;

    public function __construct($db) {
        $this->ofertaLaboral = new OfertaLaboral($db);
        $this->postulacionModel = new Postulacion($db);
    }

    public function crearOferta($data) {
        if ($this->ofertaLaboral->crearOferta($data)) {
            echo json_encode(["mensaje" => "Oferta creada correctamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al crear la oferta"]);
        }
    }

    public function editarOferta($id, $data) {
        if ($this->ofertaLaboral->editarOferta($id, $data)) {
            echo json_encode(["mensaje" => "Oferta actualizada correctamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al actualizar la oferta"]);
        }
    }

    public function verPostulantes($ofertaId) {
        $postulantes = $this->postulacionModel->listarPostulantesPorOferta($ofertaId);
        echo json_encode($postulantes);
    }

    public function actualizarEstadoPostulacion($id, $estado, $comentario) {
        if ($this->postulacion->actualizarEstado($id, $estado, $comentario)) {
            echo json_encode(["mensaje" => "Estado de la postulación actualizado correctamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al actualizar el estado de la postulación"]);
        }
    }

    public function desactivarOferta($id) {
        if ($this->ofertaLaboral->desactivarOferta($id)) {
            echo json_encode(["mensaje" => "Oferta desactivada correctamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al desactivar la oferta"]);
        }
    }
}
?>