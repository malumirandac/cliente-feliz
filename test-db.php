<?php
// CORS y cabeceras
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Incluir la clase de conexión
require_once './config/database.php';

// Instanciar y probar conexión
$database = new Database();
$conn = $database->getConnection();

if ($conn) {
    try {
        // Prueba simple: ejecutar un SELECT 1
        $stmt = $conn->query("SELECT 1");
        echo json_encode(["conexion" => "exitosa"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Fallo la ejecución de prueba: " . $e->getMessage()]);
    }
} else {
    http_response_code(500);
    echo json_encode(["error" => "No se pudo establecer conexión con la base de datos."]);
}
?>
