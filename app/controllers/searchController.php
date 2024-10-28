<?php

namespace app\controllers;
// Define el espacio de nombres (namespace) para el controlador, organizando así el código y evitando conflictos de nombres.

use app\models\mainModel;
// Importa la clase mainModel del espacio de nombres app\models. Permite usar esta clase dentro del controlador sin tener que escribir el namespace completo.

class searchController extends mainModel
// Define la clase searchController, que extiende (hereda de) la clase mainModel, permitiendo acceder a sus métodos y propiedades.
{

    /*----------  Controlador modulos de busquedas  ----------*/
    // Este comentario indica que la siguiente función maneja los módulos de búsqueda.

    public function modulosBusquedaControlador($modulo)
    // Declara el método modulosBusquedaControlador, que toma un parámetro llamado $modulo. Este método es público, lo que significa que puede ser llamado desde fuera de la clase.
    {

        $listaModulos = ['userSearch'];
        // Define un array llamado $listaModulos que contiene los módulos de búsqueda permitidos. En este caso, solo contiene 'userSearch'.

        if (in_array($modulo, $listaModulos)) {
            // Comprueba si el valor de $modulo está en el array $listaModulos. Si está presente, ejecuta el bloque de código dentro del if.

            return false; // Devuelve false si el módulo está permitido.
        } else {
            return true; // Devuelve true si el módulo no está permitido.
        }
    }


    /*----------  Controlador iniciar busqueda  ----------*/
    // Este comentario indica que la siguiente función inicia el proceso de búsqueda.

    public function iniciarBuscadorControlador()
    // Declara el método iniciarBuscadorControlador, que no recibe parámetros.
    {

        $url = $this->limpiarCadena($_POST['modulo_url']);
        // Llama al método limpiarCadena (se asume que está definido en mainModel) para limpiar la URL del módulo, usando la entrada del formulario enviada mediante POST.

        $texto = $this->limpiarCadena($_POST['txt_buscador']);
        // Limpia el texto del buscador que fue enviado mediante POST, asegurando que no contenga caracteres no deseados.

        if ($this->modulosBusquedaControlador($url)) {
            // Llama al método modulosBusquedaControlador pasando la URL limpia. Si devuelve true (es decir, el módulo no está permitido), ejecuta el bloque dentro del if.

            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No podemos procesar la petición en este momento",
                "icono" => "error"
            ];
            return json_encode($alerta);
            // Crea un array $alerta que contiene información sobre el error y lo devuelve como un JSON.
            exit(); // Termina la ejecución del script.
        }

        if ($texto == "") {
            // Comprueba si el texto del buscador está vacío. Si lo está, ejecuta el bloque dentro del if.

            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "Introduce un término de búsqueda",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit(); // Termina la ejecución del script.
        }

        if ($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}", $texto)) {
            // Llama al método verificarDatos que esta en mainModel para validar que el texto cumpla con el formato especificado. Si no coincide, ejecuta el bloque dentro del if.

            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "El término de búsqueda no coincide con el formato solicitado",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit(); // Termina la ejecución del script.
        }

        $_SESSION[$url] = $texto;
        // Almacena el texto del buscador en la sesión, utilizando la URL limpia como clave. Esto permite mantener el estado de la búsqueda.

        $alerta = [
            "tipo" => "redireccionar",
            "url" => APP_URL . $url . "/"
        ];
        // Prepara un array $alerta que indica que se debe redirigir a la URL construida a partir de APP_URL y el módulo.

        return json_encode($alerta);
        // Devuelve el array $alerta como un JSON, permitiendo que el cliente lo maneje (por ejemplo, para redirigir).
    }


    /*----------  Controlador eliminar busqueda  ----------*/
    // Este comentario indica que la siguiente función se encarga de eliminar la búsqueda.

    public function eliminarBuscadorControlador()
    // Declara el método eliminarBuscadorControlador, que no recibe parámetros.
    {

        $url = $this->limpiarCadena($_POST['modulo_url']);
        // Limpia la URL del módulo usando la entrada enviada por POST.

        if ($this->modulosBusquedaControlador($url)) {
            // Llama a modulosBusquedaControlador con la URL limpia. Si devuelve true (el módulo no está permitido), ejecuta el bloque dentro del if.

            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "texto" => "No podemos procesar la petición en este momento",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit(); // Termina la ejecución del script.
        }

        unset($_SESSION[$url]);
        // Elimina el valor asociado con la URL limpia de la sesión, limpiando así los datos de búsqueda almacenados.

        $alerta = [
            "tipo" => "redireccionar",
            "url" => APP_URL . $url . "/"
        ];
        // Prepara un array $alerta que indica que se debe redirigir a la URL correspondiente.

        return json_encode($alerta);
        // Devuelve el array $alerta como un JSON, indicando que la operación se realizó con éxito.
    }
}
