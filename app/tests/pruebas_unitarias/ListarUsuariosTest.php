<?php


/*
     
vendor/bin/phpunit tests/pruebas_unitarias/ListarUsuariosTest.php 

vendor/bin/phpunit --debug tests/pruebas_unitarias/ListarUsuariosTest.php


*/



namespace app\controllers;

require_once __DIR__ . '/../../autoload.php';
require_once __DIR__ . "/../../config/app.php";
if (!defined('APP_URL')) {
    define('APP_URL', 'http://localhost/crud_proyecto/');
}

use PHPUnit\Framework\TestCase;
use app\controllers\userController;

class ListarUsuariosTest extends TestCase
{
    private $userController;

    protected function setUp(): void
    {
        // Inicializa la sesión simulada
        $_SESSION['id'] = 1; // Asigna un ID de usuario simulado

        // Instancia del controlador y mockeo de métodos
        $this->userController = $this->getMockBuilder(UserController::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'limpiarCadena',
                'ejecutarConsulta',
                'paginadorTablas'
            ])
            ->getMock();
    }

    public function testListarUsuariosSinResultados()
    {
        $_GET['pagina'] = 1;
        $_GET['registros'] = 10;
        $_GET['url'] = 'usuarios';
        $_GET['busqueda'] = '';

        // Simula el resultado vacío de la consulta
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('fetchAll')->willReturn([]); // No hay datos
        $stmt->method('fetchColumn')->willReturn(0); // Total de registros es 0

        $this->userController->method('ejecutarConsulta')->willReturn($stmt);
        $this->userController->method('limpiarCadena')->willReturnCallback(function ($input) {
            return $input;
        });

        // Simula la tabla vacía
        $this->userController->method('paginadorTablas')->willReturn('<nav></nav>');

        $resultado = $this->userController->listarUsuarioControlador(1, 10, 'usuarios', '');

        $this->assertStringContainsString('No hay registros en el sistema', $resultado);
    }

    public function testListarUsuariosConResultados()
    {
        $_GET['pagina'] = 1;
        $_GET['registros'] = 10;
        $_GET['url'] = 'usuarios';
        $_GET['busqueda'] = '';

        // Simula algunos datos de usuarios
        $datos = [
            [
                'usuario_id' => 1, // o 'usuario_id' si así lo espera el código
                'usuario_nombre' => 'Juan',
                'usuario_apellido' => 'Perez',
                'usuario_usuario' => 'juan123',
                'usuario_email' => 'juan@dominio.com',
                'usuario_tipoUsuario' => 'admin',
                'usuario_creado' => '2024-12-09 12:00:00',
                'usuario_actualizado' => '2024-12-09 12:00:00'
            ]
        ];

        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('fetchAll')->willReturn($datos); // Devuelve los datos
        $stmt->method('fetchColumn')->willReturn(count($datos)); // Total de registros

        $this->userController->method('ejecutarConsulta')->willReturn($stmt);
        $this->userController->method('limpiarCadena')->willReturnCallback(function ($input) {
            return $input;
        });

        // Simula la tabla con los datos
        $this->userController->method('paginadorTablas')->willReturn('<nav></nav>');

        $resultado = $this->userController->listarUsuarioControlador(1, 10, 'usuarios', '');

        $this->assertStringContainsString('Juan Perez', $resultado);
        $this->assertStringContainsString('juan123', $resultado);
        $this->assertStringContainsString('juan@dominio.com', $resultado);
    }

    public function testListarUsuariosConBusqueda()
    {
        $_GET['pagina'] = 1;
        $_GET['registros'] = 10;
        $_GET['url'] = 'usuarios';
        $_GET['busqueda'] = 'Juan';

        // Simula algunos datos de usuarios con búsqueda activa
        $datos = [
            [
                'usuario_id' => 1, // Se añadió el índice 'usuario_id' para evitar el error
                'usuario_nombre' => 'Juan',
                'usuario_apellido' => 'Perez',
                'usuario_usuario' => 'juan123',
                'usuario_email' => 'juan@dominio.com',
                'usuario_tipoUsuario' => 'admin',
                'usuario_creado' => '2024-12-09 12:00:00',
                'usuario_actualizado' => '2024-12-09 12:00:00'
            ]
        ];

        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('fetchAll')->willReturn($datos); // Devuelve los datos
        $stmt->method('fetchColumn')->willReturn(count($datos)); // Total de registros

        $this->userController->method('ejecutarConsulta')->willReturn($stmt);
        $this->userController->method('limpiarCadena')->willReturnCallback(function ($input) {
            return $input;
        });

        // Simula la tabla con los datos y la paginación
        $this->userController->method('paginadorTablas')->willReturn('<nav></nav>');

        $resultado = $this->userController->listarUsuarioControlador(1, 10, 'usuarios', 'Juan');

        $this->assertStringContainsString('Juan Perez', $resultado);
        $this->assertStringContainsString('juan123', $resultado);
        $this->assertStringContainsString('juan@dominio.com', $resultado);
    }
}
