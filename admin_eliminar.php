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

if ($id == $_SESSION["usuario_id"]) {
    echo "
    <script>
        alert('No puedes eliminar tu propio usuario administrador.');
        window.location='admin_panel.php';
    </script>
    ";
    exit();
}

$consulta = $conn->prepare(
    "SELECT foto FROM usuarios WHERE id=?"
);

$consulta->bind_param("i", $id);
$consulta->execute();

$resultado = $consulta->get_result();

if ($resultado->num_rows == 1) {

    $usuario = $resultado->fetch_assoc();

    if (
        !empty($usuario["foto"]) &&
        $usuario["foto"] != "avatar.png"
    ) {

        $ruta = "uploads/" . $usuario["foto"];

        if (file_exists($ruta)) {
            unlink($ruta);
        }
    }

    $eliminar = $conn->prepare(
        "DELETE FROM usuarios WHERE id=?"
    );

    $eliminar->bind_param("i", $id);

    if ($eliminar->execute()) {

        echo "
        <script>
            alert('Usuario eliminado correctamente.');
            window.location='admin_panel.php';
        </script>
        ";

    } else {

        echo "
        <script>
            alert('Error al eliminar usuario.');
            window.location='admin_panel.php';
        </script>
        ";
    }

} else {

    header("Location: admin_panel.php");
    exit();
}

?>