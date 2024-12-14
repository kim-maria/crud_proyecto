<?php


/*
     
vendor/bin/phpunit tests/pruebas_unitarias/EliminarUsuarioTest.php 

vendor/bin/phpunit --debug tests/pruebas_unitarias/EliminarUsuarioTest.php


*/



namespace app\controllers;

require_once __DIR__ . '/../../autoload.php';
require_once __DIR__ . "/../../config/app.php";
if (!defined('APP_URL')) {
    define('APP_URL', 'http://localhost/crud_proyecto/');
}

use PHPUnit\Framework\TestCase;
use app\controllers\userController;

class EliminarUsuarioTest extends TestCase
{
    private $userController;

    protected function setUp(): void
    {
        // Inicializa la sesión simulada
        $_SESSION['id'] = 1; // Asigna un ID de usuario simulado

        // Instancia del controlador y mockeo de métodos
        $this->userController = $this->getMockBuilder(userController::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'limpiarCadena',
                'ejecutarConsulta',
                'eliminarRegistro'
            ])
            ->getMock();
    }

    public function testEliminarUsuarioPrincipal()
    {
        $_POST['usuario_id'] = 1; // ID del usuario principal

        // Simula la llamada y verifica que no se pueda eliminar
        $this->userController->method('limpiarCadena')->willReturn(1);

        $resultado = $this->userController->eliminarUsuarioControlador();

        $expected = [
            "tipo" => "simple",
            "titulo" => "Ocurrió un error inesperado",
            "texto" => "No podemos eliminar el usuario principal del sistema",
            "icono" => "error"
        ];

        $this->assertJsonStringEqualsJsonString(json_encode($expected), $resultado);
    }

    public function testEliminarUsuarioNoExistente()
    {
        $_POST['usuario_id'] = 2; // ID de usuario que no existe

        // Simula que no se encuentra el usuario
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('rowCount')->willReturn(0); // No encuentra el usuario

        $this->userController->method('limpiarCadena')->willReturn(2);
        $this->userController->method('ejecutarConsulta')->willReturn($stmt);

        $resultado = $this->userController->eliminarUsuarioControlador();

        $expected = [
            "tipo" => "simple",
            "titulo" => "Ocurrió un error inesperado",
            "texto" => "No hemos encontrado el usuario en el sistema",
            "icono" => "error"
        ];

        $this->assertJsonStringEqualsJsonString(json_encode($expected), $resultado);
    }

    public function testEliminarUsuarioConError()
    {
        $_POST['usuario_id'] = 2; // ID de usuario que existe

        // Simula que se encuentra el usuario
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('rowCount')->willReturn(1); // Usuario encontrado
        $stmt->method('fetch')->willReturn([
            'usuario_nombre' => 'Carlos',
            'usuario_apellido' => 'Méndez',
            'usuario_foto' => 'foto.jpg'
        ]);

        // Simula que la eliminación falla (simulamos que no se elimina correctamente)
        $eliminarStmt = $this->createMock(\PDOStatement::class);
        $eliminarStmt->method('rowCount')->willReturn(0); // Simula fallo en la eliminación

        $this->userController->method('eliminarRegistro')->willReturn($eliminarStmt);
        $this->userController->method('limpiarCadena')->willReturn(2);
        $this->userController->method('ejecutarConsulta')->willReturn($stmt);

        $resultado = $this->userController->eliminarUsuarioControlador();

        $expected = [
            "tipo" => "simple",
            "titulo" => "Ocurrió un error inesperado",
            "texto" => "No hemos podido eliminar el usuario Carlos Méndez del sistema, por favor intente nuevamente",
            "icono" => "error"
        ];

        $this->assertJsonStringEqualsJsonString(json_encode($expected), $resultado);
    }



    public function testEliminarUsuarioExitoso()
    {
        $_POST['usuario_id'] = 2; // ID de usuario que existe

        // Simula que se encuentra el usuario
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('rowCount')->willReturn(1); // Usuario encontrado
        $stmt->method('fetch')->willReturn([
            'usuario_nombre' => 'Carlos',
            'usuario_apellido' => 'Méndez',
            'usuario_foto' => 'foto.jpg'
        ]);

        // Simula que la eliminación es exitosa
        $this->userController->method('eliminarRegistro')->willReturn($stmt);

        // Evitamos mockear is_file y unlink para este caso
        $this->userController->method('limpiarCadena')->willReturn(2);
        $this->userController->method('ejecutarConsulta')->willReturn($stmt);

        $resultado = $this->userController->eliminarUsuarioControlador();

        $expected = [
            "tipo" => "recargar",
            "titulo" => "Usuario eliminado",
            "texto" => "El usuario Carlos Méndez ha sido eliminado del sistema correctamente",
            "icono" => "success"
        ];

        $this->assertJsonStringEqualsJsonString(json_encode($expected), $resultado);
    }
}
