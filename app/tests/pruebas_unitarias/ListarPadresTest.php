<?php


/*
     
vendor/bin/phpunit tests/pruebas_unitarias/ListarPadresTest.php 

vendor/bin/phpunit --debug tests/pruebas_unitarias/ListarPadresTest.php


*/



namespace app\controllers;

require_once __DIR__ . '/../../autoload.php';
require_once __DIR__ . "/../../config/app.php";
if (!defined('APP_URL')) {
    define('APP_URL', 'http://localhost/crud_proyecto/');
}

use PHPUnit\Framework\TestCase;
use app\controllers\userController;

class ListarPadresTest extends TestCase
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

    public function testListarPadresSinResultados()
    {
        $_GET['pagina'] = 1;
        $_GET['registros'] = 10;
        $_GET['url'] = 'padres';
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

        $resultado = $this->userController->listarPadresControlador(1, 10, 'padres', '');

        $this->assertStringContainsString('No hay registros en el sistema', $resultado);
    }

    public function testListarPadresConResultados()
    {
        $_GET['pagina'] = 1;
        $_GET['registros'] = 10;
        $_GET['url'] = 'padres';
        $_GET['busqueda'] = '';

        // Simula algunos datos de padres
        $datos = [
            [
                'usuario_id' => 2,
                'usuario_nombre' => 'Carlos',
                'usuario_apellido' => 'Martínez',
                'usuario_email' => 'carlos@dominio.com',
                'usuario_telefono' => '1234567890',
                'usuario_tipoUsuario' => 'Apoderado',
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

        $resultado = $this->userController->listarPadresControlador(1, 10, 'padres', '');

        $this->assertStringContainsString('Carlos Martínez', $resultado);
        $this->assertStringContainsString('carlos@dominio.com', $resultado);
        $this->assertStringContainsString('1234567890', $resultado);
    }

    public function testListarPadresConBusqueda()
    {
        $_GET['pagina'] = 1;
        $_GET['registros'] = 10;
        $_GET['url'] = 'padres';
        $_GET['busqueda'] = 'Carlos';

        // Simula algunos datos de padres con búsqueda activa
        $datos = [
            [
                'usuario_id' => 2,
                'usuario_nombre' => 'Carlos',
                'usuario_apellido' => 'Martínez',
                'usuario_email' => 'carlos@dominio.com',
                'usuario_telefono' => '1234567890',
                'usuario_tipoUsuario' => 'Apoderado',
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

        $resultado = $this->userController->listarPadresControlador(1, 10, 'padres', 'Carlos');

        $this->assertStringContainsString('Carlos Martínez', $resultado);
        $this->assertStringContainsString('carlos@dominio.com', $resultado);
        $this->assertStringContainsString('1234567890', $resultado);
    }
}
