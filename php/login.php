<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Solo se permiten peticiones POST"]);
    exit;
}

$host = 'localhost';
$db = 'tortasdb';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode(["error" => "Error de conexión: " . $conn->connect_error]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email']) || !isset($data['password'])) {
    echo json_encode(["error" => "Faltan datos"]);
    exit;
}

$email = $conn->real_escape_string($data['email']);
$password = $data['password'];

$sql = "SELECT id, nombre, password FROM usuarios WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo json_encode(["error" => "Usuario no encontrado"]);
    exit;
}

$user = $result->fetch_assoc();

if (password_verify($password, $user['password'])) {
    // Login exitoso
    echo json_encode([
        "success" => true,
        "message" => "Login correcto",
        "user" => [
            "id" => $user['id'],
            "nombre" => $user['nombre'],
            "email" => $email
        ]
    ]);
} else {
    echo json_encode(["error" => "Contraseña incorrecta"]);
}

$conn->close();
?>
