<?php

$host = "localhost";
$usuario = "root";
$password = "";
$base_datos = "perfil_usuario_db";
$puerto = 3307;

$conn = new mysqli($host, $usuario, $password, $base_datos, $puerto);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

//echo "Conexión exitosa";

?>
