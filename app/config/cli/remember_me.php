<?php

use DI\ContainerBuilder;
use App\Services\RememberMeService;
use Dotenv\Dotenv;

// Ajuste o caminho conforme sua estrutura
$root = dirname(__DIR__);

// Autoload
require $root . '/vendor/autoload.php';

// Carregar variáveis de ambiente
$dotenv = Dotenv::createImmutable($root);
$dotenv->safeLoad();

// Carregar config da aplicação
$appConfig = require $root . '/app/config/app.php';

// Criar container
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(
    $root . '/app/config/dependencies.php'
);

// Compilar se produção
if ($appConfig['env'] === 'production') {
    $containerBuilder->enableCompilation($root . '/storage/cache');
}

$container = $containerBuilder->build();

/** @var RememberMeService $rememberMeService */
$rememberMeService = $container->get(RememberMeService::class);

// Executar limpeza
$rememberMeService->deleteRememberMe();

echo "Tokens expirados removidos com sucesso.\n";
