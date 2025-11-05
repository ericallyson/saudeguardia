<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

/**
 * Laravel - Um framework PHP para artesãos da Web.
 *
 * Este arquivo é o ponto de entrada para todas as solicitações que entram no aplicativo.
 * Ele carrega o autoloader do Composer e inicia o kernel do Laravel.
 */

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Verificação do Autoloader
|--------------------------------------------------------------------------
|
| O autoloader gerado pelo Composer carrega automaticamente todas as
| classes do framework e do seu aplicativo. Se ele não existir, o
| projeto ainda não foi instalado corretamente.
|
*/

if (!file_exists(__DIR__.'/../vendor/autoload.php')) {
    echo 'O autoloader do Composer não foi encontrado. Execute <code>composer install</code> na raiz do projeto.';
    exit(1);
}

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Inicialização da Aplicação
|--------------------------------------------------------------------------
|
| Agora precisamos criar uma instância da aplicação Laravel, que serve
| como um contêiner de IoC e um ponto de partida para o framework.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Execução da Aplicação
|--------------------------------------------------------------------------
|
| Após ter a aplicação, podemos lidar com a solicitação HTTP
| recebida e enviar a resposta de volta ao navegador do cliente.
|
*/

$kernel = $app->make(Kernel::class);

$response = tap($kernel->handle(
    $request = Request::capture()
))->send();

$kernel->terminate($request, $response);
