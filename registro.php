<?php

include("conexion.php");

$mensaje = "";
$tipoMensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $cedula = trim($_POST["cedula"]);
    $nombres = trim($_POST["nombres"]);
    $apellidos = trim($_POST["apellidos"]);
    $username = trim($_POST["username"]);
    $correo = trim($_POST["correo"]);
    $password = $_POST["password"];
    $confirmar_password = $_POST["confirmar_password"];

    if (
        empty($cedula) ||
        empty($nombres) ||
        empty($apellidos) ||
        empty($username) ||
        empty($correo) ||
        empty($password) ||
        empty($confirmar_password)
    ) {
        $mensaje = "Todos los campos son obligatorios.";
        $tipoMensaje = "error";

    } elseif (!ctype_digit($cedula)) {
        $mensaje = "La cédula solo debe contener números.";
        $tipoMensaje = "error";

    } elseif (strlen($cedula) != 10) {
        $mensaje = "La cédula debe tener 10 dígitos.";
        $tipoMensaje = "error";

    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "Correo no válido.";
        $tipoMensaje = "error";

    } elseif ($password != $confirmar_password) {
        $mensaje = "Las contraseñas no coinciden.";
        $tipoMensaje = "error";

    } elseif (
        strlen($password) < 8 ||
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[a-z]/', $password) ||
        !preg_match('/[0-9]/', $password) ||
        !preg_match('/[\W]/', $password)
    ) {
        $mensaje = "La contraseña debe tener mínimo 8 caracteres, mayúscula, minúscula, número y símbolo.";
        $tipoMensaje = "error";

    } else {

        $verificar = $conn->prepare(
            "SELECT id FROM usuarios 
             WHERE cedula = ? OR correo = ? OR username = ?"
        );

        $verificar->bind_param("sss", $cedula, $correo, $username);
        $verificar->execute();
        $resultado = $verificar->get_result();

        if ($resultado->num_rows > 0) {
            $mensaje = "La cédula, correo o nombre de usuario ya están registrados.";
            $tipoMensaje = "error";

        } else {

            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $foto = "avatar.png";
            $tema = "light";

            $stmt = $conn->prepare(
                "INSERT INTO usuarios 
                (cedula, nombres, apellidos, username, correo, password, foto, tema)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "ssssssss",
                $cedula,
                $nombres,
                $apellidos,
                $username,
                $correo,
                $password_hash,
                $foto,
                $tema
            );

            if ($stmt->execute()) {
                $mensaje = "Usuario registrado correctamente.";
                $tipoMensaje = "success";
            } else {
                $mensaje = "Error al registrar usuario.";
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

<title>Registro de Usuario</title>

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
    width:420px;
    padding:35px;
    padding-top:40px;
    border-radius:20px;
    box-shadow:0 10px 25px rgba(0,0,0,0.2);
}

.logo{
    width:130px;
    display:block;
    margin:0 auto 25px auto;
    filter: drop-shadow(0 5px 10px rgba(0,0,0,0.2));
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

input{
    width:100%;
    padding:12px;
    margin-top:6px;
    margin-bottom:18px;
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

.password-container{
    position:relative;
}

.password-container input{
    padding-right:45px;
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
    color:#666;
    margin-top:-12px;
    margin-bottom:15px;
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
    transition:0.3s;
}

button:hover{
    background:#372fcf;
}

.login{
    text-align:center;
    margin-top:20px;
}

.login a{
    color:#4f46e5;
    text-decoration:none;
    font-weight:bold;
}
</style>
</head>

<body>

<div class="contenedor">

<img src="img/logo.png" class="logo">

<h2>Registro de Usuario</h2>

<form method="POST">

    <label>Cédula</label>
    <input type="text" name="cedula" placeholder="Ingrese su cédula" maxlength="10" pattern="[0-9]+" required>

    <label>Nombres</label>
    <input type="text" name="nombres" placeholder="Ingrese sus nombres" required>

    <label>Apellidos</label>
    <input type="text" name="apellidos" placeholder="Ingrese sus apellidos" required>

    <label>Nombre de usuario</label>
    <input type="text" name="username" placeholder="Ejemplo: paulz" required>

    <label>Correo electrónico</label>
    <input type="email" name="correo" placeholder="Ingrese su correo" required>

    <label>Contraseña</label>
    <div class="password-container">
        <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" required>
        <span class="toggle-password" onclick="togglePassword('password', this)">👁</span>
    </div>

    <div class="ayuda">
        Mínimo 8 caracteres, mayúscula, minúscula, número y símbolo.
    </div>

    <label>Confirmar contraseña</label>
    <div class="password-container">
        <input type="password" id="confirmar_password" name="confirmar_password" placeholder="Confirme su contraseña" required>
        <span class="toggle-password" onclick="togglePassword('confirmar_password', this)">👁</span>
    </div>

    <button type="submit">Registrarse</button>

</form>

<div class="login">
    <a href="login.php">Ya tengo cuenta</a>
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
