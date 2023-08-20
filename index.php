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
$app->addErrorMiddleware(true, true, true);

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

$app->post('/call', function (Request $request, Response $response)  use ($app) {
	$logger = new Logger('app');

	$processor = new UidProcessor();
	$logger->pushProcessor($processor);

	$handler = new StreamHandler(__DIR__ . '/../logs/app.log', Logger::DEBUG);
	$logger->pushHandler($handler);

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

			$response->getBody()->write(func($params,new DeepClientPhpWrapper($jwt, $url)));
		} catch (Exception $e) {
			$response->getBody()->write("An error occurred: ".$e->getMessage());
			$logger->info("An error occurred: ".$e->getMessage());
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
