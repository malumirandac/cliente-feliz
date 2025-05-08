<?php
require_once './models/Postulacion.php';

class PostulacionController {
    private $postulacion;

    public function __construct($db) {
        $this->postulacion = new Postulacion($db);
    }

    public function postular($data) {
        $resultado = $this->postulacion->postular($data);
        echo json_encode(["mensaje" => $resultado ? "Postulación creada" : "Error al postular"]);
    }


    public function listarPostulacionesPorOferta($oferta_laboral_id) {
        if (empty($oferta_laboral_id)) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID de oferta laboral no proporcionado"]);
            return;
        }
    
        $postulaciones = $this->postulacion->listarPostulantesPorOferta($oferta_laboral_id);
        if (!empty($postulaciones)) {
            http_response_code(200);
            echo json_encode(["data" => $postulaciones]);
        } else {
            http_response_code(404);
            echo json_encode(["mensaje" => "No se encontraron postulaciones para esta oferta laboral"]);
        }
    }

    public function actualizarEstado($id, $data) {
        if (empty($id) || empty($data['estado_postulacion']) || empty($data['comentario'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID, estado_postulacion o comentario no proporcionado"]);
            return;
        }
    
        $resultado = $this->postulacion->actualizarEstado($id, $data['estado_postulacion'], $data['comentario']);
        if ($resultado) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Estado de postulación actualizado correctamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "No se pudo actualizar el estado de la postulación"]);
        }
    }

    public function obtenerPostulacion($id) {
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID de postulación no proporcionado"]);
            return;
        }
    
        $postulacion = $this->postulacion->obtenerPostulacionPorId($id);
        if ($postulacion) {
            http_response_code(200);
            echo json_encode(["data" => $postulacion]);
        } else {
            http_response_code(404);
            echo json_encode(["mensaje" => "Postulación no encontrada"]);
        }
    }

    public function obtenerPostulacionesPorCandidato($id_usuario) {
        if (empty($id_usuario)) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID de usuario no proporcionado"]);
            return;
        }
    
        $postulaciones = $this->postulacion->obtenerPostulacionesPorCandidato($id_usuario);
        if (!empty($postulaciones)) {
            http_response_code(200);
            echo json_encode(["data" => $postulaciones]);
        } else {
            http_response_code(404);
            echo json_encode(["mensaje" => "No se encontraron postulaciones para este usuario"]);
        }
    }

    

}

?>