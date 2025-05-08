<?php
// index.php principal adaptado a tu estructura y lógica actual

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE');
header('Content-Type: application/json');

require_once './config/database.php';
require_once './controllers/CandidatoController.php';
require_once './controllers/ReclutadorController.php';
require_once './controllers/UsuarioController.php';
require_once './controllers/AntecedenteController.php';
require_once './controllers/OfertaLaboralController.php';
require_once './controllers/PostulacionController.php';

$database = new Database();
$db = $database->getConnection();

$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$segments = explode('/', $path);

// Si el proyecto está dentro de /CLIENTE_FELIZ, el recurso va en la posición 1
$recurso = $segments[1] ?? '';
$id = $segments[2] ?? null;

$method = $_SERVER['REQUEST_METHOD'];

$candidatoController = new CandidatoController($db);
$reclutadorController = new ReclutadorController($db);
$usuarioController = new UsuarioController($db);
$antecedenteController = new AntecedenteController($db);
$ofertaController = new OfertaLaboralController($db);
$postulacionController = new PostulacionController($db);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

switch ($recurso) {
    case 'usuarios':
        if ($method === 'POST') {
            $inputData = json_decode(file_get_contents("php://input"), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(["mensaje" => "Datos JSON inválidos"]);
                break;
            }
            $usuarioController->insertarUsuario($inputData);
        } elseif ($method === 'GET') {
            $usuarioController->obtenerUsuarios();
        } elseif ($method === 'PUT') {
            $inputData = json_decode(file_get_contents("php://input"), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(["mensaje" => "Datos JSON inválidos"]);
                break;
            }
            $usuarioController->actualizarUsuario($id, $inputData);
        } elseif ($method === 'PATCH') {
            $inputData = json_decode(file_get_contents("php://input"), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(["mensaje" => "Datos JSON inválidos"]);
                break;
            }
            $usuarioController->actualizarParcialUsuario($id, $inputData);
        } elseif ($method === 'DELETE') {
            if (empty($id)) {
                http_response_code(400);
                echo json_encode(["mensaje" => "ID de usuario no proporcionado"]);
                break;
            }
            $usuarioController->eliminarUsuario($id);
        } else {
            http_response_code(405);
            echo json_encode(["mensaje" => "Método no permitido"]);
        }
        break;

    case 'candidatos':
        if ($method === 'GET') {
            $candidatoController->verOfertasActivas();
        } elseif ($method === 'POST') {
            $candidatoController->postular(json_decode(file_get_contents("php://input"), true));
        }
        break;

    case 'reclutadores':
        $inputData = json_decode(file_get_contents("php://input"), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(["mensaje" => "Datos JSON inválidos"]);
        break;
    }

    if ($method === 'POST') {
        $reclutadorController->crearOferta($inputData);
    } elseif ($method === 'PUT') {
        $reclutadorController->editarOferta($id, $inputData);
    } elseif ($method === 'DELETE') {
        $reclutadorController->desactivarOferta($id);
    } elseif ($method === 'GET') {
        $reclutadorController->verPostulantes($id);
    } else {
        http_response_code(405);
        echo json_encode(["mensaje" => "Método no permitido"]);
    }
    break;

    case 'antecedentes-academicos':
        if ($method === 'GET') {
            $antecedenteController->obtenerAcademicos($id);
        } elseif ($method === 'POST') {
            $antecedenteController->insertarAcademico(json_decode(file_get_contents("php://input"), true));
        }
        break;

    case 'antecedentes-laborales':
        if ($method === 'GET') {
            $antecedenteController->obtenerLaborales($id);
        } elseif ($method === 'POST') {
            $antecedenteController->insertarLaboral(json_decode(file_get_contents("php://input"), true));
        }
        break;

    case 'ofertas':
    if ($method === 'GET') {
        $ofertaController->listar(); // Llama al método listar del controlador
    } elseif ($method === 'POST') {
        $inputData = json_decode(file_get_contents("php://input"), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Datos JSON inválidos"]);
            break;
        }
        $ofertaController->crearOferta($inputData);
    } elseif ($method === 'PUT') {
        $inputData = json_decode(file_get_contents("php://input"), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Datos JSON inválidos"]);
            break;
        }
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID de oferta no proporcionado"]);
            break;
        }
        $ofertaController->actualizarOferta($id, $inputData);
    } elseif ($method === 'PATCH') {
        $inputData = json_decode(file_get_contents("php://input"), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Datos JSON inválidos"]);
            break;
        }
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID de oferta no proporcionado"]);
            break;
        }
        $ofertaController->actualizarParcialOferta($id, $inputData);
    } elseif ($method === 'DELETE') {
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID de oferta no proporcionado"]);
            break;
        }
        $ofertaController->eliminarOferta($id);
    } else {
        http_response_code(405);
        echo json_encode(["mensaje" => "Método no permitido"]);
    }
    break;

    case 'postulantes':
        if ($method === 'GET') {
            if (empty($id)) {
                http_response_code(400);
                echo json_encode(["mensaje" => "ID de oferta no proporcionado"]);
                break;
            }
            $ofertaController->listarPostulantes($id);
        }
        break;

        case 'postulaciones':
            if ($method === 'GET') {
                if (!empty($id)) {
                    $postulacionController->listarPostulacionesPorOferta($id); // Llama al método para listar postulaciones por oferta
                } else {
                    $postulacionController->obtenerPostulaciones($id); // Llama al método general para obtener postulaciones
                }
            } elseif ($method === 'POST') {
                $postulacionController->crearPostulacion(json_decode(file_get_contents("php://input"), true));
            } elseif ($method === 'PATCH') {
                $inputData = json_decode(file_get_contents("php://input"), true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    http_response_code(400);
                    echo json_encode(["mensaje" => "Datos JSON inválidos"]);
                    break;
                }
                if (empty($id)) {
                    http_response_code(400);
                    echo json_encode(["mensaje" => "ID de postulación no proporcionado"]);
                    break;
                }
                $postulacionController->actualizarEstado($id, $inputData);
            } else {
                http_response_code(405);
                echo json_encode(["mensaje" => "Método no permitido"]);
            }
            break;

    default:
        http_response_code(404);
        echo json_encode(["mensaje" => "Ruta no encontrada"]);
        break;
}
?>