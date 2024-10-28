<?php

namespace app\models;
// Define el espacio de nombres (namespace) para el modelo. Esto ayuda a organizar el código y evita conflictos de nombres en la aplicación.

use \PDO;
// Importa la clase PDO de PHP, que se utiliza para interactuar con bases de datos a través de PHP Data Objects.

if (file_exists(__DIR__ . "/../../config/server.php")) {
    // Comprueba si el archivo de configuración del servidor existe en la ruta especificada.
    require_once __DIR__ . "/../../config/server.php";
    // Si el archivo existe, se incluye en el script. Este archivo debe contener las constantes de conexión a la base de datos.
}

class mainModel
// Define la clase mainModel, que contendrá métodos para interactuar con la base de datos.
{

    // Propiedades privadas para almacenar la información de conexión a la base de datos.
    private $server = DB_SERVER; // Nombre del servidor de la base de datos.
    private $db = DB_NAME;       // Nombre de la base de datos.
    private $user = DB_USER;     // Usuario para la conexión a la base de datos.
    private $pass = DB_PASS;     // Contraseña para la conexión a la base de datos.


    /*----------  Funcion conectar a BD  ----------*/
    protected function conectar()
    // Declara el método protegido conectar, que se encargará de establecer una conexión a la base de datos.
    {
        // Crea una nueva instancia de PDO para conectar a la base de datos MySQL utilizando la información de conexión.
        $conexion = new PDO("mysql:host=" . $this->server . ";dbname=" . $this->db, $this->user, $this->pass);

        // Establece el conjunto de caracteres de la conexión a UTF-8 para soportar caracteres especiales.
        $conexion->exec("SET CHARACTER SET utf8");

        return $conexion; // Devuelve el objeto de conexión.
    }


    /*----------  Funcion ejecutar consultas  ----------*/
    protected function ejecutarConsulta($consulta)
    // Declara el método protegido ejecutarConsulta, que recibe una consulta SQL como parámetro.
    {
        // Prepara la consulta utilizando la conexión a la base de datos.
        $sql = $this->conectar()->prepare($consulta);

        $sql->execute(); // Ejecuta la consulta preparada.

        return $sql; // Devuelve el objeto PDOStatement resultante de la ejecución de la consulta.
    }


    /*----------  Funcion limpiar cadenas  ----------*/
    public function limpiarCadena($cadena)
    // Declara el método público limpiarCadena, que se utiliza para sanitizar cadenas de texto.
    {
        // Define un arreglo de palabras y patrones que se desean eliminar de la cadena.
        $palabras = ["<script>", "</script>", "<script src", "<script type=", "SELECT * FROM", "SELECT ", " SELECT ", "DELETE FROM", "INSERT INTO", "DROP TABLE", "DROP DATABASE", "TRUNCATE TABLE", "SHOW TABLES", "SHOW DATABASES", "<?php", "?>", "--", "^", "<", ">", "==", "=", ";", "::"];

        $cadena = trim($cadena); // Elimina espacios en blanco al principio y al final de la cadena.
        $cadena = stripslashes($cadena); // Elimina las barras invertidas de la cadena.

        // Recorre cada palabra/patrón definido en el arreglo y los reemplaza con una cadena vacía.
        foreach ($palabras as $palabra) {
            $cadena = str_ireplace($palabra, "", $cadena);
        }

        $cadena = trim($cadena); // Elimina espacios en blanco nuevamente tras la limpieza.
        $cadena = stripslashes($cadena); // Elimina nuevamente las barras invertidas.

        return $cadena; // Devuelve la cadena limpia.
    }


    /*---------- Funcion verificar datos (expresion regular) ----------*/
    protected function verificarDatos($filtro, $cadena)
    // Declara el método protegido verificarDatos, que recibe un filtro (expresión regular) y una cadena para verificar.
    {
        // Utiliza preg_match para comprobar si la cadena cumple con el filtro. 
        // Si coincide, devuelve false (indica que los datos son válidos); de lo contrario, devuelve true.
        if (preg_match("/^" . $filtro . "$/", $cadena)) {
            return false; // Si la cadena coincide con el filtro, no hay error.
        } else {
            return true; // Si no coincide, hay un error en los datos.
        }
    }


    /*----------  Funcion para ejecutar una consulta INSERT preparada  ----------*/
    protected function guardarDatos($tabla, $datos)
    // Declara el método protegido guardarDatos, que se encarga de insertar datos en una tabla.
    {

        // Inicia la construcción de la consulta SQL para insertar datos.
        $query = "INSERT INTO $tabla (";

        $C = 0; // Contador para manejar la coma en la lista de campos.
        foreach ($datos as $clave) {
            // Recorre cada elemento de $datos para construir la lista de campos en la consulta.
            if ($C >= 1) {
                $query .= ","; // Agrega una coma antes del campo si no es el primero.
            }
            $query .= $clave["campo_nombre"]; // Agrega el nombre del campo a la consulta.
            $C++; // Incrementa el contador.
        }

        $query .= ") VALUES("; // Agrega la parte VALUES a la consulta.

        $C = 0; // Reinicia el contador para la lista de valores.
        foreach ($datos as $clave) {
            // Recorre nuevamente $datos para construir la lista de valores.
            if ($C >= 1) {
                $query .= ","; // Agrega una coma antes del valor si no es el primero.
            }
            $query .= $clave["campo_marcador"]; // Agrega el marcador de posición para el valor.
            $C++; // Incrementa el contador.
        }

        $query .= ")"; // Finaliza la construcción de la consulta.

        // Prepara la consulta SQL construida.
        $sql = $this->conectar()->prepare($query);

        // Asocia los valores a los marcadores de posición en la consulta preparada.
        foreach ($datos as $clave) {
            $sql->bindParam($clave["campo_marcador"], $clave["campo_valor"]);
        }

        $sql->execute(); // Ejecuta la consulta preparada.

        return $sql; // Devuelve el objeto PDOStatement resultante de la ejecución de la consulta.
    }


    /*---------- Funcion seleccionar datos ----------*/
    public function seleccionarDatos($tipo, $tabla, $campo, $id)
    // Declara la función seleccionarDatos, que recibe cuatro parámetros: $tipo, $tabla, $campo e $id.
    // Esta función es pública, lo que significa que puede ser llamada desde fuera de la clase.
    {
        $tipo = $this->limpiarCadena($tipo);
        // Llama al método limpiarCadena para limpiar el parámetro $tipo, posiblemente eliminando caracteres no deseados o peligrosos (prevención de inyección SQL).

        $tabla = $this->limpiarCadena($tabla);
        // Llama al método limpiarCadena para limpiar el parámetro $tabla.

        $campo = $this->limpiarCadena($campo);
        // Llama al método limpiarCadena para limpiar el parámetro $campo.

        $id = $this->limpiarCadena($id);
        // Llama al método limpiarCadena para limpiar el parámetro $id.

        if ($tipo == "Unico") {
            // Comprueba si el tipo de selección es "Unico". Si es así, ejecuta el siguiente bloque de código.

            $sql = $this->conectar()->prepare("SELECT * FROM $tabla WHERE $campo=:ID");
            // Prepara una consulta SQL para seleccionar todos los campos de la tabla especificada ($tabla) donde el valor del campo ($campo) coincide con un ID dado.
            // Utiliza un marcador de posición (bind parameter) ":ID" para evitar inyecciones SQL.

            $sql->bindParam(":ID", $id);
            // Asocia el marcador de posición ":ID" con el valor de $id, que se utilizará en la consulta SQL preparada.
        } elseif ($tipo == "Normal") {
            // Si el tipo de selección es "Normal", ejecuta el siguiente bloque de código.

            $sql = $this->conectar()->prepare("SELECT $campo FROM $tabla");
            // Prepara una consulta SQL para seleccionar solo el campo específico ($campo) de la tabla especificada ($tabla).
        }

        $sql->execute();
        // Ejecuta la consulta SQL preparada.

        return $sql;
        // Devuelve el objeto de la consulta SQL ejecutada, que contiene los resultados de la selección.
    }


    /*----------  Funcion para ejecutar una consulta UPDATE preparada  ----------*/
    protected function actualizarDatos($tabla, $datos, $condicion)
    // Declara la función actualizarDatos, que recibe tres parámetros: $tabla, $datos y $condicion.
    // Esta función es protegida, lo que significa que solo puede ser llamada desde la misma clase o desde clases derivadas.
    {
        $query = "UPDATE $tabla SET ";
        // Inicia una cadena de consulta SQL para actualizar la tabla especificada ($tabla).

        $C = 0; // Contador para manejar la coma en la consulta.
        foreach ($datos as $clave) {
            // Itera a través de los datos proporcionados en el array $datos.
            if ($C >= 1) {
                $query .= ",";
                // Si el contador es mayor o igual a 1, añade una coma a la cadena de consulta para separar los campos.
            }
            $query .= $clave["campo_nombre"] . "=" . $clave["campo_marcador"];
            // Añade a la cadena de consulta el nombre del campo y el marcador correspondiente para la actualización.
            $C++;
            // Incrementa el contador.
        }

        $query .= " WHERE " . $condicion["condicion_campo"] . "=" . $condicion["condicion_marcador"];
        // Completa la cadena de consulta añadiendo la cláusula WHERE, que define la condición para la actualización.

        $sql = $this->conectar()->prepare($query);
        // Prepara la consulta SQL para su ejecución.

        foreach ($datos as $clave) {
            // Itera nuevamente a través de los datos para asociar los valores a los marcadores de posición.
            $sql->bindParam($clave["campo_marcador"], $clave["campo_valor"]);
            // Asocia el marcador de posición con el valor correspondiente que se utilizará en la consulta de actualización.
        }

        $sql->bindParam($condicion["condicion_marcador"], $condicion["condicion_valor"]);
        // Asocia el marcador de posición de la condición con el valor correspondiente de la condición para la cláusula WHERE.

        $sql->execute();
        // Ejecuta la consulta SQL preparada, realizando la actualización en la base de datos.

        return $sql;
        // Devuelve el objeto de la consulta SQL ejecutada, que puede incluir información sobre la actualización realizada.
    }

    /*---------- Funcion eliminar registro ----------*/
    // Este es un comentario que indica que la siguiente función se encarga de eliminar un registro.

    protected function eliminarRegistro($tabla, $campo, $id)
    // Declara el método eliminarRegistro, que es protegido (protected), lo que significa que solo puede ser accedido por esta clase o sus subclases. 
    // Recibe tres parámetros: $tabla (nombre de la tabla), $campo (nombre del campo para la condición) y $id (valor del campo que se desea eliminar).
    {
        $sql = $this->conectar()->prepare("DELETE FROM $tabla WHERE $campo=:id");
        // Llama al método conectar() (que se supone establece una conexión a la base de datos), y prepara una consulta SQL de eliminación 
        // donde se indica que se eliminará de la tabla especificada ($tabla) donde el campo especificado ($campo) es igual a un valor determinado (:id).

        $sql->bindParam(":id", $id);
        // Vincula el parámetro :id en la consulta SQL al valor del argumento $id. Esto previene ataques de inyección SQL y asegura que el valor se pase correctamente.

        $sql->execute();
        // Ejecuta la consulta SQL preparada. Esto realiza la eliminación del registro en la base de datos.

        return $sql;
        // Devuelve el objeto de la consulta SQL después de su ejecución. Esto puede ser útil para verificar si la operación fue exitosa o para manejar errores.
    }


    /*---------- Paginador de tablas ----------*/
    // Este es un comentario que indica que la siguiente función se encarga de crear un paginador para las tablas.
    protected function paginadorTablas($pagina, $numeroPaginas, $url, $botones)
    // Declara el método paginadorTablas, que es protegido y recibe cuatro parámetros: 
    // $pagina (número actual de la página), $numeroPaginas (total de páginas), 
    // $url (base de la URL para los enlaces) y $botones (número máximo de botones de paginación a mostrar).
    {
        $tabla = '<nav class="pagination is-centered is-rounded" role="navigation" aria-label="pagination">';
        // Inicializa la variable $tabla con el HTML básico para la estructura de navegación del paginador. 

        if ($pagina <= 1) {
            // Comprueba si la página actual es la primera. Si es así, ejecuta el bloque de código dentro de este if.

            $tabla .= '
            <a class="pagination-previous is-disabled" disabled >Anterior</a>
            <ul class="pagination-list">
            ';
            // Agrega el enlace "Anterior" deshabilitado y la lista de elementos de paginación, ya que no hay páginas anteriores.
        } else {
            // Si no es la primera página, ejecuta el bloque de código dentro de este else.

            $tabla .= '
            <a class="pagination-previous" href="' . $url . ($pagina - 1) . '/">Anterior</a>
            <ul class="pagination-list">
                <li><a class="pagination-link" href="' . $url . '1/">1</a></li>
                <li><span class="pagination-ellipsis">&hellip;</span></li>
            ';
            // Agrega el enlace "Anterior" con un enlace a la página anterior y el primer número de página, seguido de un elipsis.
        }

        $ci = 0; // Inicializa un contador para los botones de paginación.

        for ($i = $pagina; $i <= $numeroPaginas; $i++) {
            // Inicia un bucle for que itera desde la página actual hasta el número total de páginas.

            if ($ci >= $botones) {
                // Comprueba si el contador ha alcanzado el número máximo de botones que se deben mostrar. Si es así, se sale del bucle.
                break;
            }

            if ($pagina == $i) {
                // Comprueba si el número de página actual es igual al índice del bucle.

                $tabla .= '<li><a class="pagination-link is-current" href="' . $url . $i . '/">' . $i . '</a></li>';
                // Si es la página actual, agrega un enlace que indica que es la página activa.
            } else {
                $tabla .= '<li><a class="pagination-link" href="' . $url . $i . '/">' . $i . '</a></li>';
                // Si no es la página actual, agrega un enlace normal para esa página.
            }

            $ci++; // Incrementa el contador de botones.
        }

        if ($pagina == $numeroPaginas) {
            // Comprueba si la página actual es la última página. Si es así, ejecuta el bloque de código dentro de este if.

            $tabla .= '
            </ul>
            <a class="pagination-next is-disabled" disabled >Siguiente</a>
            ';
            // Agrega la lista de paginación y un enlace "Siguiente" deshabilitado, ya que no hay más páginas.
        } else {
            // Si no es la última página, ejecuta el bloque de código dentro de este else.

            $tabla .= '
                <li><span class="pagination-ellipsis">&hellip;</span></li>
                <li><a class="pagination-link" href="' . $url . $numeroPaginas . '/">' . $numeroPaginas . '</a></li>
            </ul>
            <a class="pagination-next" href="' . $url . ($pagina + 1) . '/">Siguiente</a>
            ';
            // Agrega un elipsis, un enlace a la última página, y un enlace "Siguiente" que lleva a la siguiente página.
        }

        $tabla .= '</nav>';
        // Cierra la estructura de navegación del paginador.

        return $tabla;
        // Devuelve el HTML completo del paginador, que puede ser utilizado para mostrarlo en la interfaz de usuario.
    }
}
