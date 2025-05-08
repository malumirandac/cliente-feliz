<?php

require_once './models/OfertaLaboral.php';

class OfertaLaboralController {
    private $oferta;

    public function __construct($db) {
        $this->oferta = new OfertaLaboral($db);
    }

    public function listarOfertasActivas() {
        $ofertas = $this->oferta->obtenerOfertasActivas();
        if (!empty($ofertas)) {
            http_response_code(200);
            echo json_encode(["data" => $ofertas]);
        } else {
            http_response_code(404);
            echo json_encode(["mensaje" => "No se encontraron ofertas laborales activas"]);
        }
    }

    public function crearOferta($data) {
        if (!empty($data['titulo']) && !empty($data['descripcion']) && !empty($data['salario']) && !empty($data['reclutador_id'])) {
            $resultado = $this->oferta->crearOferta($data); // Cambiado de $this->ofertaLaboral a $this->oferta
            if ($resultado) {
                http_response_code(201);
                echo json_encode(["mensaje" => "Oferta laboral creada correctamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["mensaje" => "No se pudo crear la oferta laboral"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["mensaje" => "Faltan campos requeridos"]);
        }
    }

    public function desactivar($id) {
        $resultado = $this->oferta->desactivar($id);
        echo json_encode(["mensaje" => $resultado ? "Oferta desactivada" : "Error al desactivar oferta"]);
    }

    public function actualizarOferta($id, $data) {
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID de oferta no proporcionado"]);
            return;
        }
    
        if (!empty($data)) {
            $resultado = $this->oferta->actualizarOferta($id, $data);
            if ($resultado) {
                http_response_code(200);
                echo json_encode(["mensaje" => "Oferta laboral actualizada correctamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["mensaje" => "No se pudo actualizar la oferta laboral"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["mensaje" => "No se proporcionaron datos para actualizar"]);
        }
    }

    public function actualizarParcialOferta($id, $data) {
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID de oferta no proporcionado"]);
            return;
        }
    
        if (!empty($data)) {
            $resultado = $this->oferta->actualizarOferta($id, $data);
            if ($resultado) {
                http_response_code(200);
                echo json_encode(["mensaje" => "Oferta laboral actualizada parcialmente"]);
            } else {
                http_response_code(500);
                echo json_encode(["mensaje" => "No se pudo actualizar la oferta laboral"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["mensaje" => "No se proporcionaron datos para actualizar"]);
        }
    }

    public function eliminarOferta($id) {
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID de oferta no proporcionado"]);
            return;
        }
    
        $resultado = $this->oferta->eliminarOferta($id);
        if ($resultado) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Oferta laboral eliminada correctamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "No se pudo eliminar la oferta laboral"]);
        }
    }

    public function listarPostulantes($ofertaId) {
        if (empty($ofertaId)) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID de oferta no proporcionado"]);
            return;
        }
    
        $postulantes = $this->oferta->obtenerPostulantesPorOferta($ofertaId);
        if (!empty($postulantes)) {
            http_response_code(200);
            echo json_encode(["data" => $postulantes]);
        } else {
            http_response_code(404);
            echo json_encode(["mensaje" => "No se encontraron postulantes para esta oferta"]);
        }
    }

    public function actualizarEstado($postulacionId, $data) {
        if (empty($postulacionId) || empty($data['estado'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID de postulación o estado no proporcionado"]);
            return;
        }
    
        $resultado = $this->postulacion->actualizarEstadoPostulacion($postulacionId, $data['estado']);
        if ($resultado) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Estado de postulación actualizado correctamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "No se pudo actualizar el estado de la postulación"]);
        }
    }

}

?>