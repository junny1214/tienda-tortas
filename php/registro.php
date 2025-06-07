<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *"); // Si usas frontend separado
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0); // Preflight para CORS
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Solo se permiten peticiones POST"]);
    exit;
}

// Conexión
$host = 'localhost';
$db = 'tortasdb';  // Asegúrate que este sea el nombre correcto
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode(["error" => "Error de conexión: " . $conn->connect_error]);
    exit;
}

// Captura y decodifica JSON
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['nombre'], $data['email'], $data['password'])) {
    echo json_encode(["error" => "Faltan datos"]);
    exit;
}

$nombre = $conn->real_escape_string($data['nombre']);
$email = $conn->real_escape_string($data['email']);
$password = password_hash($data['password'], PASSWORD_DEFAULT);

// Verifica si ya existe ese correo
$sql_check = "SELECT id FROM usuarios WHERE email = '$email'";
$result = $conn->query($sql_check);
if ($result->num_rows > 0) {
    echo json_encode(["error" => "El correo ya está registrado"]);
    exit;
}

// Inserta el usuario
$sql = "INSERT INTO usuarios (nombre, email, password) VALUES ('$nombre', '$email', '$password')";
if ($conn->query($sql)) {
    echo json_encode(["success" => true, "message" => "Usuario registrado correctamente"]);
} else {
    echo json_encode(["error" => "Error al registrar usuario: " . $conn->error]);

}

$conn->close();
?>
