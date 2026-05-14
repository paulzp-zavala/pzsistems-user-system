<?php

session_start();
include("conexion.php");

$mensaje = "";
$tipoMensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $correo = trim($_POST["correo"]);
    $password = $_POST["password"];

    if (empty($correo) || empty($password)) {
        $mensaje = "Ingrese correo y contraseña.";
        $tipoMensaje = "error";

    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "Correo no válido.";
        $tipoMensaje = "error";

    } else {

        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();

        $resultado = $stmt->get_result();

        if ($resultado->num_rows == 1) {

            $usuario = $resultado->fetch_assoc();

            if ($usuario["estado"] != "activo") {
                $mensaje = "Su usuario se encuentra inactivo.";
                $tipoMensaje = "error";

            } elseif (password_verify($password, $usuario["password"])) {

                $_SESSION["usuario_id"] = $usuario["id"];
                $_SESSION["nombres"] = $usuario["nombres"];
                $_SESSION["username"] = $usuario["username"];
                $_SESSION["tema"] = $usuario["tema"];
                $_SESSION["rol"] = $usuario["rol"];

                if ($usuario["rol"] == "admin") {
                    header("Location: admin_panel.php");
                } else {
                    header("Location: perfil.php");
                }

                exit();

            } else {
                $mensaje = "Contraseña incorrecta.";
                $tipoMensaje = "error";
            }

        } else {
            $mensaje = "Correo no registrado.";
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

<title>Inicio de Sesión</title>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
body{
    font-family: Arial, Helvetica, sans-serif;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
    margin:0;
}

.contenedor{
    background:white;
    width:400px;
    padding:35px;
    padding-top:40px;
    border-radius:20px;
    box-shadow:0 10px 25px rgba(0,0,0,0.2);
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

.registro{
    text-align:center;
    margin-top:20px;
}

.registro a{
    color:#4f46e5;
    text-decoration:none;
    font-weight:bold;
}
</style>
</head>

<body>

<div class="contenedor">

<img src="img/logo.png" class="logo">

<h2>Inicio de Sesión</h2>

<form method="POST">

    <label>Correo electrónico</label>
    <input type="email" name="correo" placeholder="Ingrese su correo" required>

    <label>Contraseña</label>
    <div class="password-container">
        <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" required>
        <span class="toggle-password" onclick="togglePassword('password', this)">👁</span>
    </div>

    <button type="submit">Ingresar</button>

</form>

<div class="registro">
    <a href="registro.php">Crear una cuenta</a>
</div>

</div>

<?php if (!empty($mensaje)): ?>
<script>
Swal.fire({
    icon: "<?php echo $tipoMensaje; ?>",
    title: "Atención",
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