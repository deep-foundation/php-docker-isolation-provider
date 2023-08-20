<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

extension_loaded('deep_client_php_extension') or dl('deep_client_php_extension.so');

require 'vendor/autoload.php';

use Monolog\Handler\StreamHandler;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$logger = new Logger('app');

$processor = new UidProcessor();
$logger->pushProcessor($processor);

$handler = new StreamHandler(__DIR__ . '/../logs/app.log', Logger::DEBUG);
$logger->pushHandler($handler);

$errorMiddleware->setDefaultErrorHandler(function (Request $request, Throwable $exception, bool $displayErrorDetails)  use ($app, $logger) {
	$response = $app->getResponseFactory()->createResponse();
	$logger->info("An error occurred: ".$exception->getMessage());
	$response->getBody()->write($exception->getMessage());
	return $response;
});

// Define app routes
$app->get('/', function (Request $request, Response $response) {
	$response->getBody()->write('{}');
	return $response;
});

$app->get('/healthz', function (Request $request, Response $response) {
	$response->getBody()->write('{}');
	return $response;
});

$app->post('/init', function (Request $request, Response $response) {
	$response->getBody()->write('{}');
	return $response;
});

$app->post('/call', function (Request $request, Response $response) use ($app, $logger) {
	$data = json_decode((string)$request->getBody(), true);

	if ($data) {
		try {
			$params = $data['params'] ?? [];
			$code = $params['code'] ?? '';
			$jwt = $params['jwt'] ?? '';
			$url = sprintf("http://%s", getenv('GQL_URN') ?: '192.168.135:3006/gql');

			$codeFn = str_replace('function fn(', 'function func(', $code);
			$codeFn = str_replace("\\n", "\n", $codeFn);

			eval($codeFn);

			$result = var_export(func(
					$data = $params,
					$deep = new DeepClientPhpWrapper($jwt, $url)
				), true);

			$response->getBody()->write($result);
		} catch (Exception $e) {
			$logger->info("An error occurred: ".$e->getMessage());
			$response->getBody()->write("An error occurred: ".$e->getMessage());
		}
	} else {
		$logger->info('Failed to parse JSON.');
		$response->getBody()->write('Failed to parse JSON.');
	}
	return $response;
});

$app->post('/http-call', function (Request $request, Response $response) {
	$response->getBody()->write('{}');
	return $response;
});

// Run app
$app->run();
