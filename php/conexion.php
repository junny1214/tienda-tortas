<?php
$servername = "localhost";
$user = "root";      // usuario de XAMPP por defecto
$pass = "";          // contraseña vacía por defecto
$dbname = "tortasdb";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(["error" => "Conexión fallida: " . $conn->connect_error]));
}
?>
