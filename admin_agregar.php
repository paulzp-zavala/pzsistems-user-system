<?php

session_start();
include("conexion.php");

if (!isset($_SESSION["usuario_id"]) || $_SESSION["rol"] != "admin") {
    header("Location: login.php");
    exit();
}

$mensaje = "";
$tipoMensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $cedula = trim($_POST["cedula"]);
    $nombres = trim($_POST["nombres"]);
    $apellidos = trim($_POST["apellidos"]);
    $username = trim($_POST["username"]);
    $correo = trim($_POST["correo"]);
    $password = $_POST["password"];
    $rol = $_POST["rol"];
    $estado = $_POST["estado"];
    $tema = $_POST["tema"];

    if (
        empty($cedula) ||
        empty($nombres) ||
        empty($apellidos) ||
        empty($username) ||
        empty($correo) ||
        empty($password)
    ) {

        $mensaje = "Todos los campos son obligatorios.";
        $tipoMensaje = "error";

    } elseif (!ctype_digit($cedula)) {

        $mensaje = "La cédula solo debe contener números.";
        $tipoMensaje = "error";

    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $mensaje = "Correo electrónico inválido.";
        $tipoMensaje = "error";

    } else {

        $verificar = $conn->prepare(
            "SELECT id FROM usuarios
             WHERE cedula=? OR correo=? OR username=?"
        );

        $verificar->bind_param(
            "sss",
            $cedula,
            $correo,
            $username
        );

        $verificar->execute();

        $resultado = $verificar->get_result();

        if ($resultado->num_rows > 0) {

            $mensaje = "La cédula, correo o usuario ya existen.";
            $tipoMensaje = "error";

        } else {

            $password_hash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $foto = "avatar.png";

            $insertar = $conn->prepare(
                "INSERT INTO usuarios
                (cedula, nombres, apellidos, username, correo, password, foto, tema, rol, estado)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $insertar->bind_param(
                "ssssssssss",
                $cedula,
                $nombres,
                $apellidos,
                $username,
                $correo,
                $password_hash,
                $foto,
                $tema,
                $rol,
                $estado
            );

            if ($insertar->execute()) {

                $mensaje = "Usuario agregado correctamente.";
                $tipoMensaje = "success";

            } else {

                $mensaje = "Error al agregar usuario.";
                $tipoMensaje = "error";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Agregar Usuario</title>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>

body{
    font-family: Arial, Helvetica, sans-serif;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    display:flex;
    justify-content:center;
    align-items:center;
    min-height:100vh;
    margin:0;
}

.contenedor{
    background:white;
    width:500px;
    padding:35px;
    border-radius:20px;
    box-shadow:0 10px 25px rgba(0,0,0,0.2);
}

.logo{
    width:120px;
    display:block;
    margin:0 auto 20px auto;
}

h2{
    text-align:center;
    color:#4f46e5;
    margin-bottom:25px;
}

label{
    font-weight:bold;
    color:#333;
}

input,
select{
    width:100%;
    padding:12px;
    margin-top:6px;
    margin-bottom:18px;
    border:1px solid #ccc;
    border-radius:10px;
    font-size:15px;
    box-sizing:border-box;
}

button{
    width:100%;
    padding:12px;
    background:#4f46e5;
    color:white;
    border:none;
    border-radius:10px;
    font-size:16px;
    cursor:pointer;
}

button:hover{
    background:#372fcf;
}

.volver{
    text-align:center;
    margin-top:20px;
}

.volver a{
    color:#4f46e5;
    text-decoration:none;
    font-weight:bold;
}

</style>
</head>

<body>

<div class="contenedor">

<img src="img/logo.png" class="logo">

<h2>Agregar Usuario</h2>

<form method="POST">

<label>Cédula</label>
<input type="text" name="cedula" required>

<label>Nombres</label>
<input type="text" name="nombres" required>

<label>Apellidos</label>
<input type="text" name="apellidos" required>

<label>Usuario</label>
<input type="text" name="username" required>

<label>Correo</label>
<input type="email" name="correo" required>

<label>Contraseña</label>
<input type="password" name="password" required>

<label>Rol</label>
<select name="rol">

<option value="usuario">
Usuario
</option>

<option value="admin">
Administrador
</option>

</select>

<label>Estado</label>
<select name="estado">

<option value="activo">
Activo
</option>

<option value="inactivo">
Inactivo
</option>

</select>

<label>Tema</label>
<select name="tema">

<option value="light">
Claro
</option>

<option value="dark">
Oscuro
</option>

</select>

<button type="submit">
Agregar usuario
</button>

</form>

<div class="volver">
    <a href="admin_panel.php">Volver al panel</a>
</div>

</div>

<?php if (!empty($mensaje)): ?>

<script>

Swal.fire({
    icon: "<?php echo $tipoMensaje; ?>",
    title: "Sistema",
    text: "<?php echo $mensaje; ?>",
    confirmButtonColor: "#4f46e5"
});

</script>

<?php endif; ?>

</body>
</html>