<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["error" => "No se recibieron datos"]);
    exit;
}

$nombre = trim($data['nombre'] ?? '');
$email = trim($data['email'] ?? '');
$mensaje = trim($data['mensaje'] ?? '');

if (empty($nombre) || empty($email) || empty($mensaje)) {
    echo json_encode(["error" => "Todos los campos son obligatorios"]);
    exit;
}

// Datos de conexión
$servername = "localhost";
$username = "root";     // usa $username para mantener coherencia
$password = "";         // contraseña vacía
$dbname = "tortasdb";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    echo json_encode(["error" => "Conexión fallida: " . $conn->connect_error]);
    exit;
}

// Preparar consulta segura para insertar datos
$stmt = $conn->prepare("INSERT INTO mensajes_contacto (nombre, email, mensaje) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $nombre, $email, $mensaje);

if ($stmt->execute()) {
    echo json_encode(["success" => "Mensaje recibido correctamente"]);
} else {
    echo json_encode(["error" => "Error al guardar en la base de datos"]);
}

$stmt->close();
$conn->close();
?>
