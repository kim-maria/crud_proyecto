<?php

namespace app\controllers;
// Define el espacio de nombres (namespace) para el controlador. Esto ayuda a organizar el código y evitar conflictos de nombres en la aplicación.

use app\models\viewsModel;
// Importa la clase viewsModel del espacio de nombres app\models. Esto permite utilizar la clase viewsModel dentro de este controlador sin necesidad de escribir el espacio de nombres completo.

class viewsController extends viewsModel
// Define la clase viewsController, que extiende (hereda de) la clase viewsModel. Esto significa que viewsController tiene acceso a todos los métodos y propiedades de viewsModel.
{

    /*---------- Controlador obtener vistas ----------*/
    // Este es un comentario que indica que la siguiente función se encarga de obtener vistas.

    public function obtenerVistasControlador($vista)
    // Declara el método obtenerVistasControlador, que recibe un parámetro llamado $vista. Este método es público, lo que significa que puede ser llamado desde fuera de la clase.
    {
        if ($vista != "") {
            // Comprueba si $vista no está vacío. Si hay un valor en $vista, ejecuta el bloque de código dentro de este if.

            $respuesta = $this->obtenerVistasModelo($vista);
            // Llama al método obtenerVistasModelo (que se supone está definido en viewsModel) pasando el argumento $vista. 
            // El resultado de esta llamada se almacena en la variable $respuesta.
        } else {
            // Si $vista está vacío, ejecuta el bloque de código dentro de este else.

            $respuesta = "login";
            // Asigna el valor "login" a la variable $respuesta. Esto indica que, por defecto, si no se proporciona una vista, se retorna la vista de inicio de sesión.
        }
        return $respuesta;
        // Devuelve el valor de $respuesta, que será el resultado de obtener la vista correspondiente o "login" si no se proporcionó ninguna vista.
    }
}
