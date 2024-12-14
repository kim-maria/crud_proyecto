<?php


/*

vendor/bin/phpunit tests/pruebas_unitarias/ListarProfesoresTest.php 

vendor/bin/phpunit --debug tests/pruebas_unitarias/ListarProfesoresTest.php

*/


namespace app\controllers;

require_once __DIR__ . '/../../autoload.php';
require_once __DIR__ . "/../../config/app.php";
if (!defined('APP_URL')) {
    define('APP_URL', 'http://localhost/crud_proyecto/');
}

use PHPUnit\Framework\TestCase;
use app\controllers\userController;

class ListarProfesoresTest extends TestCase
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

    public function testListarProfesoresSinResultados()
    {
        $_GET['pagina'] = 1;
        $_GET['registros'] = 10;
        $_GET['url'] = 'profesores';
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

        $resultado = $this->userController->listarProfesoresControlador(1, 10, 'profesores', '');

        $this->assertStringContainsString('No hay registros en el sistema', $resultado);
    }

    public function testListarProfesoresConResultados()
    {
        $_GET['pagina'] = 1;
        $_GET['registros'] = 10;
        $_GET['url'] = 'profesores';
        $_GET['busqueda'] = '';

        // Simula algunos datos de profesores
        $datos = [
            [
                'usuario_id' => 1,
                'usuario_nombre' => 'Carlos',
                'usuario_apellido' => 'Méndez',
                'usuario_email' => 'carlos@dominio.com',
                'usuario_telefono' => '123456789',
                'usuario_tipoUsuario' => 'Profesor',
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

        $resultado = $this->userController->listarProfesoresControlador(1, 10, 'profesores', '');

        $this->assertStringContainsString('Carlos Méndez', $resultado);
        $this->assertStringContainsString('carlos@dominio.com', $resultado);
        $this->assertStringContainsString('123456789', $resultado);
    }

    public function testListarProfesoresConBusqueda()
    {
        $_GET['pagina'] = 1;
        $_GET['registros'] = 10;
        $_GET['url'] = 'profesores';
        $_GET['busqueda'] = 'Carlos';

        // Simula algunos datos de profesores con búsqueda activa
        $datos = [
            [
                'usuario_id' => 1,
                'usuario_nombre' => 'Carlos',
                'usuario_apellido' => 'Méndez',
                'usuario_email' => 'carlos@dominio.com',
                'usuario_telefono' => '123456789',
                'usuario_tipoUsuario' => 'Profesor',
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

        $resultado = $this->userController->listarProfesoresControlador(1, 10, 'profesores', 'Carlos');

        $this->assertStringContainsString('Carlos Méndez', $resultado);
        $this->assertStringContainsString('carlos@dominio.com', $resultado);
        $this->assertStringContainsString('123456789', $resultado);
    }
}
