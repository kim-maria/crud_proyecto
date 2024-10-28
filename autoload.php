<?php

spl_autoload_register(function ($clase) {
    // Registra una función de autoload que se llama automáticamente cuando se intenta usar una clase que no ha sido incluida aún. 
    // La función recibe un parámetro $clase, que representa el nombre de la clase que se quiere cargar.

    $archivo = __DIR__ . "/" . $clase . ".php";
    // Define la variable $archivo que contiene la ruta del archivo que debe incluirse. 
    // __DIR__ es una constante mágica que devuelve el directorio actual del script. 
    // Se concatena con el nombre de la clase y la extensión ".php" para formar la ruta completa del archivo de la clase.

    $archivo = str_replace("\\", "/", $archivo);
    // Reemplaza las barras invertidas (\) por barras normales (/) en la ruta del archivo. 
    // Esto es útil para asegurar que la ruta sea compatible con diferentes sistemas operativos (por ejemplo, Windows usa \ mientras que Linux y macOS usan /).

    if (is_file($archivo)) {
        // Comprueba si el archivo existe en la ruta especificada. 
        // La función is_file() devuelve true si el archivo existe y es un archivo regular.

        require_once $archivo;
        // Si el archivo existe, lo incluye una sola vez en el script actual. 
        // La función require_once evita que se incluya el mismo archivo más de una vez, lo que ayuda a prevenir errores de redeclaración de clases.
    }
});
