<?php

namespace app\controllers;
// Define el espacio de nombres (namespace) para el controlador, lo que ayuda a organizar el código y evitar conflictos de nombres.

use app\models\mainModel;
// Importa la clase mainModel del espacio de nombres app\models. Esto permite utilizar la clase mainModel dentro de este controlador sin necesidad de escribir el espacio de nombres completo.

class loginController extends mainModel
// Define la clase loginController, que extiende (hereda de) la clase mainModel. Esto significa que loginController tiene acceso a todos los métodos y propiedades de mainModel.
{

    /*----------  Controlador iniciar sesión  ----------*/
    // Este es un comentario que indica que el siguiente método es responsable de iniciar sesión.

    public function iniciarSesionControlador()
    // Declara el método iniciarSesionControlador, que no recibe parámetros. Este método es público, lo que significa que puede ser llamado desde fuera de la clase.
    {

        $usuario = $this->limpiarCadena($_POST['login_usuario']);
        // Obtiene el valor del campo 'login_usuario' del formulario y lo limpia. El resultado se almacena en la variable $usuario.

        $clave = $this->limpiarCadena($_POST['login_clave']);
        // Obtiene el valor del campo 'login_clave' del formulario y lo limpia de manera similar. El resultado se almacena en la variable $clave.

        # Verificando campos obligatorios #
        if ($usuario == "" || $clave == "") {
            // Comprueba si $usuario o $clave están vacíos. Si cualquiera de los dos está vacío, ejecuta el bloque de código dentro de este if.
            echo "<script>
			        Swal.fire({
					  icon: 'error',
					  title: 'Ocurrió un error inesperado',
					  text: 'No has llenado todos los campos que son obligatorios',
                      confirmButtonText: 'Aceptar'
					});
				</script>";
            // Muestra un mensaje de error usando SweetAlert indicando que se deben llenar todos los campos obligatorios.
        } else {

            # Verificando integridad de los datos #
            if ($this->verificarDatos("[a-zA-Z0-9]{4,20}", $usuario)) {
                // Llama al método verificarDatos que se encuentra definido en mainModel para comprobar si el $usuario coincide con el formato especificado (4 a 20 caracteres alfanuméricos).
                echo "<script>
				        Swal.fire({
						  icon: 'error',
						  title: 'Ocurrió un error inesperado',
						  text: 'El USUARIO no coincide con el formato solicitado',
                          confirmButtonText: 'Aceptar'
						});
					</script>";
                // Si la verificación falla, muestra un mensaje de error indicando que el formato del usuario es incorrecto.
            } else {

                # Verificando integridad de los datos #
                if ($this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $clave)) {
                    // Similar a la verificación anterior, se comprueba que $clave coincida con el formato solicitado (7 a 100 caracteres, que pueden incluir letras, números y algunos caracteres especiales).
                    echo "<script>
					        Swal.fire({
							  icon: 'error',
							  title: 'Ocurrió un error inesperado',
							  text: 'La CLAVE no coincide con el formato solicitado',
                              confirmButtonText: 'Aceptar'
							});
						</script>";
                    // Si la verificación falla, se muestra un mensaje de error para la clave.
                } else {

                    # Verificando usuario #
                    $check_usuario = $this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_usuario='$usuario'");
                    // Ejecuta una consulta SQL para buscar en la base de datos un registro de usuario que coincida con el valor de $usuario. El resultado se almacena en la variable $check_usuario.

                    if ($check_usuario->rowCount() == 1) {
                        // Comprueba si la consulta ha devuelto exactamente un registro. Si es así, se ejecuta el bloque de código dentro de este if.

                        $check_usuario = $check_usuario->fetch();
                        // Recupera el registro de usuario de la consulta y lo almacena en la variable $check_usuario.

                        if ($check_usuario['usuario_usuario'] == $usuario && password_verify($clave, $check_usuario['usuario_clave'])) {
                            // Verifica que el nombre de usuario coincida y que la clave proporcionada coincida con la clave almacenada (usando password_verify para validar una contraseña hasheada).

                            $_SESSION['id'] = $check_usuario['usuario_id'];
                            $_SESSION['nombre'] = $check_usuario['usuario_nombre'];
                            $_SESSION['apellido'] = $check_usuario['usuario_apellido'];
                            $_SESSION['usuario'] = $check_usuario['usuario_usuario'];
                            $_SESSION['foto'] = $check_usuario['usuario_foto'];
                            // Si las credenciales son correctas, almacena la información del usuario en la sesión para su uso posterior.

                            if (headers_sent()) {
                                // Comprueba si las cabeceras HTTP ya han sido enviadas. Si es así, utiliza JavaScript para redirigir al usuario a la página del dashboard.
                                echo "<script> window.location.href='" . APP_URL . "dashboard/'; </script>";
                            } else {
                                header("Location: " . APP_URL . "dashboard/");
                                // Si no se han enviado las cabeceras, redirige directamente al dashboard usando la función header.
                            }
                        } else {
                            // Si las credenciales no son correctas, muestra un mensaje de error.
                            echo "<script>
							        Swal.fire({
									  icon: 'error',
									  title: 'Ocurrió un error inesperado',
									  text: 'Usuario o clave incorrectos',
                                      confirmButtonText: 'Aceptar'
									});
								</script>";
                        }
                    } else {
                        // Si no se encontró ningún usuario coincidente, muestra un mensaje de error.
                        echo "<script>
						        Swal.fire({
								  icon: 'error',
								  title: 'Ocurrió un error inesperado',
								  text: 'Usuario o clave incorrectos',
                                  confirmButtonText: 'Aceptar'
								});
							</script>";
                    }
                }
            }
        }
    }

    public function cerrarSesionControlador()
    // Declara el método cerrarSesionControlador, que no recibe parámetros y es responsable de cerrar la sesión del usuario.
    {
        session_destroy();
        // Llama a session_destroy para cerrar la sesión actual y eliminar todos los datos de sesión.

        if (headers_sent()) {
            // Comprueba si las cabeceras HTTP ya han sido enviadas. Si es así, utiliza JavaScript para redirigir al usuario a la página de inicio de sesión.
            echo "<script> window.location.href='" . APP_URL . "login/'; </script>";
        } else {
            header("Location: " . APP_URL . "login/");
            // Si no se han enviado las cabeceras, redirige directamente a la página de inicio de sesión usando la función header.
        }
    }
}
