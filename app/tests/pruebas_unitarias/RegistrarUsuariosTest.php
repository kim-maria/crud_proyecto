<?php

/* !!!!!! comando en la terminal para utilizar phpunit y verificar la prueba !!!!!
     
vendor/bin/phpunit tests/pruebas_unitarias/RegistrarUsuariosTest.php 

vendor/bin/phpunit --debug tests/pruebas_unitarias/RegistrarUsuariosTest.php


*/

namespace app\controllers;

require_once __DIR__ . '/../../autoload.php';

use PHPUnit\Framework\TestCase;
use app\controllers\userController;

class RegistrarUsuariosTest extends TestCase
{
    private $userController;

    protected function setUp(): void
    {
        // Instancia del controlador y mockeo de métodos
        $this->userController = $this->getMockBuilder(UserController::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'limpiarCadena',
                'verificarDatos',
                'ejecutarConsulta',
                'guardarDatos'
            ])
            ->getMock();
    }

    public function testCamposObligatoriosVacios()
    {

        $_POST = [
            'usuario_nombre' => '',
            'usuario_apellido' => '',
            'usuario_usuario' => '',
            'usuario_email' => '',
            'usuario_clave_1' => '',
            'usuario_clave_2' => '',
            'usuario_telefono' => '',
            'usuario_tipoUsuario' => ''
        ];

        $this->userController->method('limpiarCadena')->willReturnCallback(function ($input) {
            return $input;
        });

        $resultado = $this->userController->registrarUsuarioControlador();

        $esperado = json_encode([
            "tipo" => "simple",
            "titulo" => "Ocurrió un error inesperado",
            "texto" => "No has llenado todos los campos que son obligatorios",
            "icono" => "error"
        ]);

        $this->assertEquals($esperado, $resultado);
    }

    public function testDatosInvalidos()
    {
        $_POST = [
            'usuario_nombre' => 'N',
            'usuario_apellido' => 'ApellidoValido',
            'usuario_usuario' => 'usuario123',
            'usuario_email' => 'email@dominio.com',
            'usuario_clave_1' => '123456',
            'usuario_clave_2' => '123456',
            'usuario_telefono' => '987654321',
            'usuario_tipoUsuario' => 'admin'
        ];

        $this->userController->method('limpiarCadena')->willReturnCallback(function ($input) {
            return $input;
        });

        $this->userController->method('verificarDatos')->willReturnCallback(function ($pattern, $input) {
            if ($pattern == "[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" && $input == 'N') {
                return true;
            }
            return false;
        });

        $resultado = $this->userController->registrarUsuarioControlador();

        $this->assertStringContainsString("El NOMBRE no coincide con el formato solicitado", $resultado);
    }

    public function testEmailYaRegistrado()
    {
        $_POST = [
            'usuario_nombre' => 'Juan',
            'usuario_apellido' => 'Perez',
            'usuario_usuario' => 'juan123',
            'usuario_email' => 'email@dominio.com',
            'usuario_clave_1' => '123456',
            'usuario_clave_2' => '123456',
            'usuario_telefono' => '987654321',
            'usuario_tipoUsuario' => 'admin'
        ];

        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('rowCount')->willReturn(1);

        $this->userController->method('ejecutarConsulta')->willReturn($stmt);

        $this->userController->method('limpiarCadena')->willReturnCallback(function ($input) {
            return $input;
        });
        $this->userController->method('verificarDatos')->willReturn(false);

        $resultado = $this->userController->registrarUsuarioControlador();

        $this->assertStringContainsString("El EMAIL que acaba de ingresar ya se encuentra registrado", $resultado);
    }
}
