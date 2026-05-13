<?php

session_start();
include("conexion.php");

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION["usuario_id"];

$stmt = $conn->prepare(
    "SELECT cedula, nombres, apellidos, username, correo, foto, tema, fecha_registro
     FROM usuarios
     WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

$foto = $usuario["foto"];

if ($foto == "avatar.png") {
    $ruta_foto = "img/avatar.png";
} else {
    $ruta_foto = "uploads/" . $foto;
}

$tema = $usuario["tema"];
?>

<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Perfil de Usuario</title>

<style>
body{
    font-family: Arial, Helvetica, sans-serif;
    background: <?php echo $tema == "dark" ? "#111827" : "linear-gradient(135deg, #4f46e5, #7c3aed)"; ?>;
    min-height:100vh;
    margin:0;
    color: <?php echo $tema == "dark" ? "#f9fafb" : "#111827"; ?>;
}

.navbar{
    background: <?php echo $tema == "dark" ? "#1f2937" : "white"; ?>;
    padding:15px 40px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 4px 15px rgba(0,0,0,0.2);
}

.logo{
    width:120px;
}

.nav-links a{
    margin-left:20px;
    color:#4f46e5;
    text-decoration:none;
    font-weight:bold;
}

.nav-links .logout{
    color:#dc2626;
}

.contenedor{
    max-width:900px;
    margin:50px auto;
    background: <?php echo $tema == "dark" ? "#1f2937" : "white"; ?>;
    padding:35px;
    border-radius:20px;
    box-shadow:0 10px 25px rgba(0,0,0,0.25);
    animation: aparecer 0.6s ease;
}

@keyframes aparecer{
    from{
        opacity:0;
        transform:translateY(20px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

.header-perfil{
    text-align:center;
}

.avatar{
    width:130px;
    height:130px;
    border-radius:50%;
    object-fit:cover;
    border:4px solid #4f46e5;
    margin-bottom:15px;
}

h2{
    color:#4f46e5;
}

.username{
    color: <?php echo $tema == "dark" ? "#d1d5db" : "#6b7280"; ?>;
    font-weight:bold;
}

.grid{
    display:grid;
    grid-template-columns:repeat(2, 1fr);
    gap:20px;
    margin-top:30px;
}

.card{
    background: <?php echo $tema == "dark" ? "#111827" : "#f9fafb"; ?>;
    padding:20px;
    border-radius:15px;
    border:1px solid <?php echo $tema == "dark" ? "#374151" : "#e5e7eb"; ?>;
}

.card strong{
    display:block;
    color:#4f46e5;
    margin-bottom:8px;
}

.acciones{
    margin-top:30px;
    display:flex;
    gap:15px;
    justify-content:center;
    flex-wrap:wrap;
}

.boton{
    background:#4f46e5;
    color:white;
    padding:12px 18px;
    border-radius:10px;
    text-decoration:none;
    font-weight:bold;
    transition:0.3s;
}

.boton:hover{
    background:#372fcf;
}

.boton-secundario{
    background:#111827;
}

.boton-rojo{
    background:#dc2626;
}

.boton-rojo:hover{
    background:#991b1b;
}
</style>
</head>

<body>

<div class="navbar">

<img src="img/logo.png" class="logo">

<div class="nav-links">
    <a href="perfil.php">Perfil</a>
    <a href="actualizar_perfil.php">Actualizar</a>
    <a href="cambiar_password.php">Contraseña</a>
    <a class="logout" href="logout.php">Salir</a>
</div>

</div>

<div class="contenedor">

<div class="header-perfil">

<img src="<?php echo $ruta_foto; ?>" class="avatar">

<h2>
    Bienvenido, <?php echo $usuario["nombres"]; ?>
</h2>

<div class="username">
    @<?php echo $usuario["username"]; ?>
</div>

</div>

<div class="grid">

<div class="card">
    <strong>Cédula</strong>
    <?php echo $usuario["cedula"]; ?>
</div>

<div class="card">
    <strong>Nombre completo</strong>
    <?php echo $usuario["nombres"] . " " . $usuario["apellidos"]; ?>
</div>

<div class="card">
    <strong>Correo electrónico</strong>
    <?php echo $usuario["correo"]; ?>
</div>

<div class="card">
    <strong>Fecha de registro</strong>
    <?php echo $usuario["fecha_registro"]; ?>
</div>

<div class="card">
    <strong>Tema actual</strong>
    <?php echo $tema == "dark" ? "Oscuro" : "Claro"; ?>
</div>

<div class="card">
    <strong>Estado de sesión</strong>
    Sesión activa con PHP
</div>

</div>

<div class="acciones">

<a class="boton" href="actualizar_perfil.php">
Actualizar perfil
</a>

<a class="boton" href="cambiar_password.php">
Cambiar contraseña
</a>

<a class="boton boton-rojo" href="logout.php">
Cerrar sesión
</a>

</div>

</div>

</body>
</html>