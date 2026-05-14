<?php

session_start();
include("conexion.php");

if (!isset($_SESSION["usuario_id"]) || $_SESSION["rol"] != "admin") {
    header("Location: login.php");
    exit();
}

$consulta = $conn->prepare(
    "SELECT id, cedula, nombres, apellidos, username, correo, rol, estado, fecha_registro
     FROM usuarios
     ORDER BY id DESC"
);

$consulta->execute();
$usuarios = $consulta->get_result();

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Panel Administrador</title>

<style>
body{
    font-family: Arial, Helvetica, sans-serif;
    background:#f3f4f6;
    margin:0;
}

.navbar{
    background:white;
    padding:15px 40px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 4px 15px rgba(0,0,0,0.15);
}

.logo{
    width:120px;
}

.navbar a{
    color:#dc2626;
    text-decoration:none;
    font-weight:bold;
}

.contenedor{
    max-width:1200px;
    margin:40px auto;
    background:white;
    padding:30px;
    border-radius:20px;
    box-shadow:0 10px 25px rgba(0,0,0,0.15);
}

h2{
    color:#4f46e5;
    text-align:center;
}

.btn-agregar{
    display:inline-block;
    background:#4f46e5;
    color:white;
    padding:12px 18px;
    border-radius:10px;
    text-decoration:none;
    font-weight:bold;
    margin-bottom:20px;
}

table{
    width:100%;
    border-collapse:collapse;
    font-size:14px;
}

th{
    background:#4f46e5;
    color:white;
    padding:12px;
}

td{
    padding:10px;
    border-bottom:1px solid #e5e7eb;
    text-align:center;
}

tr:hover{
    background:#f9fafb;
}

.editar{
    color:#2563eb;
    font-weight:bold;
    text-decoration:none;
}

.eliminar{
    color:#dc2626;
    font-weight:bold;
    text-decoration:none;
}

.badge{
    padding:5px 10px;
    border-radius:20px;
    font-size:12px;
    font-weight:bold;
}

.admin{
    background:#ede9fe;
    color:#6d28d9;
}

.usuario{
    background:#dbeafe;
    color:#1d4ed8;
}

.activo{
    background:#dcfce7;
    color:#166534;
}

.inactivo{
    background:#fee2e2;
    color:#991b1b;
}
</style>
</head>

<body>

<div class="navbar">
    <img src="img/logo.png" class="logo">
    <a href="logout.php">Cerrar sesión</a>
</div>

<div class="contenedor">

<h2>Panel Administrador</h2>

<a class="btn-agregar" href="admin_agregar.php">Agregar usuario</a>

<table>
    <tr>
        <th>ID</th>
        <th>Cédula</th>
        <th>Nombres</th>
        <th>Apellidos</th>
        <th>Usuario</th>
        <th>Correo</th>
        <th>Rol</th>
        <th>Estado</th>
        <th>Registro</th>
        <th>Acciones</th>
    </tr>

    <?php while($fila = $usuarios->fetch_assoc()): ?>
    <tr>
        <td><?php echo $fila["id"]; ?></td>
        <td><?php echo $fila["cedula"]; ?></td>
        <td><?php echo $fila["nombres"]; ?></td>
        <td><?php echo $fila["apellidos"]; ?></td>
        <td>@<?php echo $fila["username"]; ?></td>
        <td><?php echo $fila["correo"]; ?></td>

        <td>
            <span class="badge <?php echo $fila["rol"]; ?>">
                <?php echo $fila["rol"]; ?>
            </span>
        </td>

        <td>
            <span class="badge <?php echo $fila["estado"]; ?>">
                <?php echo $fila["estado"]; ?>
            </span>
        </td>

        <td><?php echo $fila["fecha_registro"]; ?></td>

        <td>
            <a class="editar" href="admin_editar.php?id=<?php echo $fila["id"]; ?>">Editar</a>
            |
            <a class="eliminar"
               href="admin_eliminar.php?id=<?php echo $fila["id"]; ?>"
               onclick="return confirm('¿Seguro que deseas eliminar este usuario?')">
               Eliminar
            </a>
        </td>
    </tr>
    <?php endwhile; ?>

</table>

</div>

</body>
</html>