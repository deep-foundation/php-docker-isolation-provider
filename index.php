<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$GQL_URN = getenv('GQL_URN') ?: 'localhost:3006/gql';
$GQL_SSL = (bool) getenv('GQL_SSL') ?: 0;

$logger = new Logger('app');
$logger->pushHandler(new StreamHandler('logs/app.log', Logger::DEBUG));

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

$app->post('/call', function (Request $request, Response $response) use ($logger) {
	$logger->info($request->getParsedBody());
	$response->getBody()->write('{}');
	return $response;
});

$app->post('/http-call', function (Request $request, Response $response) {
	$response->getBody()->write('{}');
	return $response;
});

// Run app
$app->run();


/*
require 'vendor/autoload.php';

use GraphQL\Client;
use GraphQL\Executor\ExecutionResult;
use GraphQL\QueryBuilder\QueryBuilder;
use GraphQL\Transport\GuzzleHttpGQLHTTPTransport;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

$app = new \Silex\Application();

$GQL_URN = getenv("GQL_URN") ?: "localhost:3006/gql";
$GQL_SSL = getenv("GQL_SSL") ?: 0;

function execute_handler($code, $args)
{
	$python_handler_context = ['args' => $args];
	$generated_code = "$code\npython_handler_context['result'] = fn(python_handler_context['args']);";
	eval($generated_code);
	$result = $python_handler_context['result'];
	return $result;
}

function make_deep_client($token)
{
	if (empty($token)) {
		throw new \InvalidArgumentException("No token provided");
	}
	$url = $GQL_SSL ? "https://$GQL_URN" : "http://$GQL_URN";
	$transport = new GuzzleHttpGQLHTTPTransport($url, ['headers' => ['Authorization' => $token]]);
	$deep_client = new Client($transport, null, null, null, true);
	return $deep_client;
}

$app->get('/healthz', function () {
	return new JsonResponse();
});

$app->post('/init', function () {
	return new JsonResponse();
});

$app->post('/call', function (Request $request) use ($app) {
	try {
		$body = json_decode($request->getContent(), true);
		$params = $body['params'];
		$args = [
			'deep' => make_deep_client($params['jwt']),
			'data' => $params['data'],
			'gql' => new QueryBuilder(),
		];
		$result = execute_handler($params['code'], $args);
		return new JsonResponse(['resolved' => $result]);
	} catch (\Exception $e) {
		return new JsonResponse(['rejected' => $e->getTraceAsString()], Response::HTTP_INTERNAL_SERVER_ERROR);
	}
});

$app->run();
*/