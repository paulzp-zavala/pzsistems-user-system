<?php

session_start();
include("conexion.php");

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION["usuario_id"];

$mensaje = "";
$tipoMensaje = "";

$stmt = $conn->prepare(
    "SELECT nombres, apellidos, username, correo, foto, tema
     FROM usuarios
     WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombres = trim($_POST["nombres"]);
    $apellidos = trim($_POST["apellidos"]);
    $username = trim($_POST["username"]);
    $correo = trim($_POST["correo"]);
    $tema = $_POST["tema"];

    $foto = $usuario["foto"];

    if (
        empty($nombres) ||
        empty($apellidos) ||
        empty($username) ||
        empty($correo) ||
        empty($tema)
    ) {
        $mensaje = "Todos los campos son obligatorios.";
        $tipoMensaje = "error";

    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "Correo no válido.";
        $tipoMensaje = "error";

    } else {

        $verificar = $conn->prepare(
            "SELECT id FROM usuarios
             WHERE (correo = ? OR username = ?)
             AND id != ?"
        );

        $verificar->bind_param("ssi", $correo, $username, $id);
        $verificar->execute();

        $resultado_verificacion = $verificar->get_result();

        if ($resultado_verificacion->num_rows > 0) {
            $mensaje = "El correo o nombre de usuario ya está registrado.";
            $tipoMensaje = "error";

        } else {

            if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] == 0) {

                $nombreArchivo = $_FILES["foto"]["name"];
                $tmp = $_FILES["foto"]["tmp_name"];
                $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));

                $extensionesPermitidas = ["jpg", "jpeg", "png", "webp"];

                if (!in_array($extension, $extensionesPermitidas)) {
                    $mensaje = "Solo se permiten imágenes JPG, JPEG, PNG o WEBP.";
                    $tipoMensaje = "error";
                } else {
                    $nuevoNombre = "usuario_" . $id . "_" . time() . "." . $extension;
                    $rutaDestino = "uploads/" . $nuevoNombre;

                    if (move_uploaded_file($tmp, $rutaDestino)) {
                        $foto = $nuevoNombre;
                    } else {
                        $mensaje = "No se pudo subir la foto.";
                        $tipoMensaje = "error";
                    }
                }
            }

            if ($tipoMensaje != "error") {

                $update = $conn->prepare(
                    "UPDATE usuarios
                     SET nombres = ?, apellidos = ?, username = ?, correo = ?, foto = ?, tema = ?
                     WHERE id = ?"
                );

                $update->bind_param(
                    "ssssssi",
                    $nombres,
                    $apellidos,
                    $username,
                    $correo,
                    $foto,
                    $tema,
                    $id
                );

                if ($update->execute()) {
                    $_SESSION["nombres"] = $nombres;
                    $_SESSION["username"] = $username;
                    $_SESSION["tema"] = $tema;

                    $mensaje = "Perfil actualizado correctamente.";
                    $tipoMensaje = "success";

                    $usuario["nombres"] = $nombres;
                    $usuario["apellidos"] = $apellidos;
                    $usuario["username"] = $username;
                    $usuario["correo"] = $correo;
                    $usuario["foto"] = $foto;
                    $usuario["tema"] = $tema;

                } else {
                    $mensaje = "Error al actualizar perfil.";
                    $tipoMensaje = "error";
                }
            }
        }
    }
}

$temaActual = $usuario["tema"];

if ($usuario["foto"] == "avatar.png") {
    $rutaFoto = "img/avatar.png";
} else {
    $rutaFoto = "uploads/" . $usuario["foto"];
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Actualizar Perfil</title>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
body{
    font-family: Arial, Helvetica, sans-serif;
    background: <?php echo $temaActual == "dark" ? "#111827" : "linear-gradient(135deg, #4f46e5, #7c3aed)"; ?>;
    min-height:100vh;
    margin:0;
    color: <?php echo $temaActual == "dark" ? "#f9fafb" : "#111827"; ?>;
}

.navbar{
    background: <?php echo $temaActual == "dark" ? "#1f2937" : "white"; ?>;
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
    max-width:500px;
    margin:50px auto;
    background: <?php echo $temaActual == "dark" ? "#1f2937" : "white"; ?>;
    padding:35px;
    border-radius:20px;
    box-shadow:0 10px 25px rgba(0,0,0,0.25);
}

h2{
    text-align:center;
    color:#4f46e5;
}

.avatar-preview{
    width:120px;
    height:120px;
    border-radius:50%;
    object-fit:cover;
    border:4px solid #4f46e5;
    display:block;
    margin:15px auto 25px auto;
}

label{
    font-weight:bold;
    display:block;
    margin-top:12px;
}

input, select{
    width:100%;
    padding:12px;
    margin-top:6px;
    border:1px solid #ccc;
    border-radius:10px;
    font-size:15px;
    box-sizing:border-box;
}

button{
    width:100%;
    padding:12px;
    margin-top:25px;
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
    font-weight:bold;
    text-decoration:none;
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

<h2>Actualizar Perfil</h2>

<img src="<?php echo $rutaFoto; ?>" class="avatar-preview">

<form method="POST" enctype="multipart/form-data">

    <label>Nombres</label>
    <input type="text" name="nombres" value="<?php echo $usuario["nombres"]; ?>" required>

    <label>Apellidos</label>
    <input type="text" name="apellidos" value="<?php echo $usuario["apellidos"]; ?>" required>

    <label>Nombre de usuario</label>
    <input type="text" name="username" value="<?php echo $usuario["username"]; ?>" required>

    <label>Correo electrónico</label>
    <input type="email" name="correo" value="<?php echo $usuario["correo"]; ?>" required>

    <label>Foto de perfil</label>
    <input type="file" name="foto" accept="image/*">

    <label>Tema</label>
    <select name="tema" required>
        <option value="light" <?php echo $usuario["tema"] == "light" ? "selected" : ""; ?>>Claro</option>
        <option value="dark" <?php echo $usuario["tema"] == "dark" ? "selected" : ""; ?>>Oscuro</option>
    </select>

    <button type="submit">Guardar cambios</button>

</form>

<div class="volver">
    <a href="perfil.php">Volver al perfil</a>
</div>

</div>

<?php if (!empty($mensaje)): ?>
<script>
Swal.fire({
    icon: "<?php echo $tipoMensaje; ?>",
    title: "<?php echo $tipoMensaje == 'success' ? 'Correcto' : 'Atención'; ?>",
    text: "<?php echo $mensaje; ?>",
    confirmButtonColor: "#4f46e5"
});
</script>
<?php endif; ?>

<?php if($tipoMensaje == "error"): ?>
<audio autoplay>
    <source src="error.mp3" type="audio/mpeg">
</audio>
<?php endif; ?>

<?php if($tipoMensaje == "success"): ?>
<audio autoplay>
    <source src="success.mp3" type="audio/mpeg">
</audio>
<?php endif; ?>

</body>
</html>