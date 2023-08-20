<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
		$params = $data['params'] ?? [];
		$code = $params['code'] ?? '';
		$jwt = $params['jwt'] ?? '';

		$logger->info( 'json_decode', [
			'params' => $params,
			'code' => $code,
			'jwt' => $jwt,
		]);

		$codeFn = str_replace('function fn(', 'function func(', $code);
		$codeFn = str_replace("\\n", "\n", $codeFn);

		eval($codeFn);

		$response->getBody()->write((string)func([
			'data' => $params,
			'deep' => new DeepClientPhpWrapper($jwt, 'http://localhost:3006/gql')
		]);

	} else {
		$logger->info('Failed to parse JSON.');
		$response->getBody()->write('Failed to parse JSON.');
	}

	$logger->info('Incoming Request:', [
		'method' => $request->getMethod(),
		'uri' => (string)$request->getUri(),
		'headers' => $request->getHeaders(),
		'body' => (string)$request->getBody(),
	]);

	$logger->info('Outgoing Response:', [
		'status' => $response->getStatusCode(),
		'headers' => $response->getHeaders(),
		'body' => (string)$response->getBody(),
	]);

	return $response;
});

$app->post('/http-call', function (Request $request, Response $response) {
	$response->getBody()->write('{}');
	return $response;
});

// Run app
$app->run();
