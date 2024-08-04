<?php

use App\CardLoader;
use App\Controller\Api\ApiCheckController;
use App\Controller\Api\ApiHintController;
use App\Controller\Api\ApiLoadingController;
use App\Controller\Api\ApiSolveController;
use App\Controller\Api\ApiTryController;
use App\Controller\GenerateController;
use App\Controller\HomepageController;
use App\Controller\PlayController;
use App\Controller\SetupController;
use App\Service\ExtendedRoute;
use App\Service\Route;
use App\Service\Router;
use App\Service\ServiceContainer;
use Twig\Environment as TwigEnv;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;

require __DIR__ . '/../vendor/autoload.php';

$rootDir = dirname(__DIR__);

$container = (new ServiceContainer())
    ->configureNamespace('App\Service')
    ->configureNamespace('App\Controller')
    ->configure(TwigEnv::class, ['__forceNew' => true])
    ->configure(LoaderInterface::class, ['__class' => FilesystemLoader::class, 'paths' => $rootDir . '/templates'])
    ->configure(Router::class, ['publicPath' => $rootDir . '/public'])
    ->configure(CardLoader::class, ['dir' => $rootDir . '/config/cards']);

$router = $container->get(Router::class);
/** @var Router $router */
$router
    ->add('', HomepageController::class)
    ->add('play/{hash}', PlayController::class)
    ->add('setup', SetupController::class)
    ->add('generate', GenerateController::class)
    ->add('api/solve', ApiSolveController::class)
    ->add('api/load/{hash}', ApiLoadingController::class)
    ->add('api/try/{hash}/{code}/{letter}', ApiTryController::class)
    ->add('api/hint/{hash}/{code}', ApiHintController::class)
    ->add('api/check/{hash}/{code}', ApiCheckController::class);

return $container;