<?php
// Incluye el archivo de configuración principal de la aplicación.
require_once "./config/app.php";
// Esto carga el archivo de configuración que probablemente contiene parámetros y ajustes necesarios para la aplicación.

// Incluye el archivo de autoload para cargar clases automáticamente.
require_once "./autoload.php";
// Esto permite que las clases sean cargadas automáticamente cuando se instancian, evitando la necesidad de incluir manualmente cada clase.

// Incluye el archivo que inicia la sesión.
require_once "./app/views/inc/session_start.php";
// Esto inicia una sesión en PHP, permitiendo el uso de variables de sesión en la aplicación.

// Verifica si hay un parámetro 'views' en la URL.
if (isset($_GET['views'])) {
    // Si existe, se separa el valor por "/" y se almacena en $url como un array.
    $url = explode("/", $_GET['views']);
} else {
    // Si no existe, se establece un array con el valor predeterminado "login".
    $url = ["login"];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <?php require_once "./app/views/inc/head.php"; // Incluye el archivo de cabecera (head) y enlaces a hojas de estilo. 
    ?>
</head>

<body>
    <?php

    // Importa las clases de controladores que se utilizarán.
    use app\controllers\viewsController;
    use app\controllers\loginController;

    // Crea una instancia del controlador de inicio de sesión.
    $insLogin = new loginController();
    // Esto permite manejar la lógica relacionada con el inicio de sesión.

    // Crea una instancia del controlador de vistas.
    $viewsController = new viewsController();
    // Esto permite manejar la lógica relacionada con las vistas de la aplicación.

    // Obtiene la vista que se debe mostrar según el primer elemento del array $url.
    $vista = $viewsController->obtenerVistasControlador($url[0]);
    // Llama al método obtenerVistasControlador del controlador de vistas, pasando el primer elemento de $url como argumento.

    // Verifica si la vista es "login" o "404" es decir no encontrado.
    if ($vista == "login" || $vista == "404") {
        // Si es así, incluye la vista correspondiente (login o 404).
        require_once "./app/views/content/" . $vista . "-view.php";
    } else {
        // Si la vista no es login o 404, verifica si el usuario está autenticado.
        if ((!isset($_SESSION['id']) || $_SESSION['id'] == "") || (!isset($_SESSION['usuario']) || $_SESSION['usuario'] == "")) {
            // Si no está autenticado, cierra la sesión y termina el script.
            $insLogin->cerrarSesionControlador();
            exit();
        }

        // Incluye la barra de navegación si el usuario está autenticado.
        require_once "./app/views/inc/navbar.php";

        // Incluye la vista correspondiente según el controlador.
        require_once $vista;
        // Esto carga la vista que corresponde al valor de $vista.
    }

    // Incluye el archivo de scripts 
    require_once "./app/views/inc/script.php";
    // Esto carga scripts de JavaScript necesarios para el funcionamiento de la aplicación.
    ?>
</body>

</html>