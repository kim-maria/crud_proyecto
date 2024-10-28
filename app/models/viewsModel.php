<?php

namespace app\models;
// Define el espacio de nombres (namespace) para el modelo. Esto ayuda a organizar el código y evitar conflictos de nombres en la aplicación.

class viewsModel
// Define la clase viewsModel. Esta clase contendrá métodos relacionados con la obtención de vistas.
{

    /*---------- Modelo obtener vista ----------*/
    // Este es un comentario que indica que la siguiente función se encarga de obtener una vista específica.

    protected function obtenerVistasModelo($vista)
    // Declara el método obtenerVistasModelo, que recibe un parámetro llamado $vista. 
    // Este método es protegido (protected), lo que significa que solo puede ser accedido desde esta clase o desde clases que hereden de ella.
    {
        // Define una lista blanca de vistas permitidas.
        $listaBlanca = ["dashboard", "userNew", "userList", "userUpdate", "userSearch", "userPhoto", "logOut"];
        // Crea un array que contiene las vistas que se consideran seguras y se pueden acceder.

        if (in_array($vista, $listaBlanca)) {
            // Comprueba si el valor de $vista está en el array $listaBlanca. 
            // Si está, ejecuta el bloque de código dentro de este if.

            if (is_file("./app/views/content/" . $vista . "-view.php")) {
                // Verifica si el archivo correspondiente a la vista existe en la ruta especificada. 
                // La ruta se construye concatenando el valor de $vista con el sufijo "-view.php".

                $contenido = "./app/views/content/" . $vista . "-view.php";
                // Si el archivo existe, asigna la ruta del archivo a la variable $contenido.
            } else {
                // Si el archivo no existe, ejecuta este bloque de código.

                $contenido = "404";
                // Asigna el valor "404" a la variable $contenido, indicando que la vista no se encontró.
            }
        } elseif ($vista == "login" || $vista == "index") {
            // Si $vista es igual a "login" o "index", ejecuta el siguiente bloque de código.

            $contenido = "login";
            // Asigna el valor "login" a la variable $contenido. Esto indica que estas vistas son válidas y se retornará "login".
        } else {
            // Si $vista no coincide con ninguna de las condiciones anteriores, ejecuta este bloque.

            $contenido = "404";
            // Asigna el valor "404" a la variable $contenido, indicando que la vista solicitada no es válida.
        }
        return $contenido;
        // Devuelve el valor de $contenido, que será la ruta de la vista encontrada o "404" si no se encontró la vista solicitada.
    }
}
