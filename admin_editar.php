<?php

session_start();
include("conexion.php");

if (!isset($_SESSION["usuario_id"]) || $_SESSION["rol"] != "admin") {
    header("Location: login.php");
    exit();
}

if (!isset($_GET["id"])) {
    header("Location: admin_panel.php");
    exit();
}

$id = intval($_GET["id"]);

$consulta = $conn->prepare(
    "SELECT * FROM usuarios WHERE id = ?"
);

$consulta->bind_param("i", $id);
$consulta->execute();

$resultado = $consulta->get_result();

if ($resultado->num_rows != 1) {
    header("Location: admin_panel.php");
    exit();
}

$usuario = $resultado->fetch_assoc();

$mensaje = "";
$tipoMensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $cedula = trim($_POST["cedula"]);
    $nombres = trim($_POST["nombres"]);
    $apellidos = trim($_POST["apellidos"]);
    $username = trim($_POST["username"]);
    $correo = trim($_POST["correo"]);
    $rol = trim($_POST["rol"]);
    $estado = trim($_POST["estado"]);
    $tema = trim($_POST["tema"]);

    if (
        empty($cedula) ||
        empty($nombres) ||
        empty($apellidos) ||
        empty($username) ||
        empty($correo)
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

        $actualizar = $conn->prepare(
            "UPDATE usuarios 
             SET cedula=?, nombres=?, apellidos=?, username=?, correo=?, rol=?, estado=?, tema=?
             WHERE id=?"
        );

        $actualizar->bind_param(
            "ssssssssi",
            $cedula,
            $nombres,
            $apellidos,
            $username,
            $correo,
            $rol,
            $estado,
            $tema,
            $id
        );

        if ($actualizar->execute()) {

            $mensaje = "Usuario actualizado correctamente.";
            $tipoMensaje = "success";

            $consulta = $conn->prepare(
                "SELECT * FROM usuarios WHERE id = ?"
            );

            $consulta->bind_param("i", $id);
            $consulta->execute();

            $resultado = $consulta->get_result();
            $usuario = $resultado->fetch_assoc();

        } else {

            $mensaje = "Error al actualizar usuario.";
            $tipoMensaje = "error";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Editar Usuario</title>

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

<h2>Editar Usuario</h2>

<form method="POST">

<label>Cédula</label>
<input type="text" name="cedula"
value="<?php echo $usuario["cedula"]; ?>" required>

<label>Nombres</label>
<input type="text" name="nombres"
value="<?php echo $usuario["nombres"]; ?>" required>

<label>Apellidos</label>
<input type="text" name="apellidos"
value="<?php echo $usuario["apellidos"]; ?>" required>

<label>Usuario</label>
<input type="text" name="username"
value="<?php echo $usuario["username"]; ?>" required>

<label>Correo</label>
<input type="email" name="correo"
value="<?php echo $usuario["correo"]; ?>" required>

<label>Rol</label>
<select name="rol">

<option value="usuario"
<?php if($usuario["rol"]=="usuario") echo "selected"; ?>>
Usuario
</option>

<option value="admin"
<?php if($usuario["rol"]=="admin") echo "selected"; ?>>
Administrador
</option>

</select>

<label>Estado</label>
<select name="estado">

<option value="activo"
<?php if($usuario["estado"]=="activo") echo "selected"; ?>>
Activo
</option>

<option value="inactivo"
<?php if($usuario["estado"]=="inactivo") echo "selected"; ?>>
Inactivo
</option>

</select>

<label>Tema</label>
<select name="tema">

<option value="light"
<?php if($usuario["tema"]=="light") echo "selected"; ?>>
Claro
</option>

<option value="dark"
<?php if($usuario["tema"]=="dark") echo "selected"; ?>>
Oscuro
</option>

</select>

<button type="submit">
Actualizar usuario
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