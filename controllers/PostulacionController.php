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

    public function crearPostulacion($data) {
        if (empty($data['candidato_id']) || empty($data['oferta_laboral_id']) || empty($data['estado_postulacion'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Faltan campos requeridos"]);
            return;
        }
    
        $resultado = $this->postulacion->crearPostulacion($data);
        if ($resultado) {
            http_response_code(201);
            echo json_encode(["mensaje" => "Postulación creada correctamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "No se pudo crear la postulación"]);
        }
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

}

?>