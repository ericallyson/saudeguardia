<?php
/**
 * Redireciona automaticamente para a pasta /public
 * caso o servidor não esteja configurado para apontar para ela.
 */

$publicPath = __DIR__ . '/public/index.php';

if (file_exists($publicPath)) {
    require_once $publicPath;
} else {
    http_response_code(500);
    echo "Erro: o arquivo /public/index.php não foi encontrado.";
}
