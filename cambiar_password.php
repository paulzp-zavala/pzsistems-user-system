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

$stmtTema = $conn->prepare("SELECT tema FROM usuarios WHERE id = ?");
$stmtTema->bind_param("i", $id);
$stmtTema->execute();
$resultadoTema = $stmtTema->get_result();
$datosTema = $resultadoTema->fetch_assoc();
$temaActual = $datosTema["tema"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $actual = $_POST["actual"];
    $nueva = $_POST["nueva"];
    $confirmar = $_POST["confirmar"];

    if (empty($actual) || empty($nueva) || empty($confirmar)) {
        $mensaje = "Todos los campos son obligatorios.";
        $tipoMensaje = "error";

    } elseif ($nueva != $confirmar) {
        $mensaje = "Las nuevas contraseñas no coinciden.";
        $tipoMensaje = "error";

    } elseif (
        strlen($nueva) < 8 ||
        !preg_match('/[A-Z]/', $nueva) ||
        !preg_match('/[a-z]/', $nueva) ||
        !preg_match('/[0-9]/', $nueva) ||
        !preg_match('/[\W]/', $nueva)
    ) {
        $mensaje = "La nueva contraseña debe tener mínimo 8 caracteres, mayúscula, minúscula, número y símbolo.";
        $tipoMensaje = "error";

    } else {

        $stmt = $conn->prepare("SELECT password FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc();

        if (password_verify($actual, $usuario["password"])) {

            $nueva_hash = password_hash($nueva, PASSWORD_DEFAULT);

            $update = $conn->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
            $update->bind_param("si", $nueva_hash, $id);

            if ($update->execute()) {
                $mensaje = "Contraseña actualizada correctamente.";
                $tipoMensaje = "success";
            } else {
                $mensaje = "Error al actualizar contraseña.";
                $tipoMensaje = "error";
            }

        } else {
            $mensaje = "La contraseña actual es incorrecta.";
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

<title>Cambiar Contraseña</title>

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

label{
    font-weight:bold;
    display:block;
    margin-top:12px;
}

.password-container{
    position:relative;
}

input{
    width:100%;
    padding:12px;
    padding-right:45px;
    margin-top:6px;
    border:1px solid #ccc;
    border-radius:10px;
    font-size:15px;
    box-sizing:border-box;
}

input:focus{
    border-color:#4f46e5;
    outline:none;
    box-shadow:0 0 8px rgba(79,70,229,0.4);
}

.toggle-password{
    position:absolute;
    right:15px;
    top:18px;
    cursor:pointer;
    font-size:18px;
}

.ayuda{
    font-size:12px;
    color: <?php echo $temaActual == "dark" ? "#d1d5db" : "#666"; ?>;
    margin-top:8px;
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

<h2>Cambiar Contraseña</h2>

<form method="POST">

    <label>Contraseña actual</label>
    <div class="password-container">
        <input type="password" id="actual" name="actual" placeholder="Ingrese contraseña actual" required>
        <span class="toggle-password" onclick="togglePassword('actual', this)">👁</span>
    </div>

    <label>Nueva contraseña</label>
    <div class="password-container">
        <input type="password" id="nueva" name="nueva" placeholder="Ingrese nueva contraseña" required>
        <span class="toggle-password" onclick="togglePassword('nueva', this)">👁</span>
    </div>

    <div class="ayuda">
        Mínimo 8 caracteres, mayúscula, minúscula, número y símbolo.
    </div>

    <label>Confirmar nueva contraseña</label>
    <div class="password-container">
        <input type="password" id="confirmar" name="confirmar" placeholder="Confirme nueva contraseña" required>
        <span class="toggle-password" onclick="togglePassword('confirmar', this)">👁</span>
    </div>

    <button type="submit">Cambiar contraseña</button>

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

<script>
function togglePassword(id, elemento){
    let input = document.getElementById(id);

    if(input.type === "password"){
        input.type = "text";
        elemento.textContent = "🙈";
    } else {
        input.type = "password";
        elemento.textContent = "👁";
    }
}
</script>

</body>
</html>