<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

extension_loaded('deep_client_php_extension') or dl('deep_client_php_extension.so');

require '/../vendor/autoload.php';

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;

$logger = new Logger('app');

$processor = new UidProcessor();
$logger->pushProcessor($processor);

$handler = new StreamHandler(__DIR__ . '/../../logs/app.log', Logger::DEBUG);
$logger->pushHandler($handler);

class CodeExecutor {
    public function execute($functionCode, $data, $deep) {
        $codeFn = "
        $functionCode
        
        print_r(func(\$data, \$deep));
        ";
        ob_start();
        set_error_handler([$this, 'errorHandler']);
        eval($codeFn);
        restore_error_handler();
        $output = ob_get_clean();
        return $output;
    }

    public function errorHandler($errno, $errstr, $errfile, $errline) {
        global $logger;
		$logger->info("Error: ".$errstr);
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
}

// Define default error handler
set_error_handler(function ($errno, $errstr, $errfile, $errline) use ($logger) {
    $logger->info("Error: " . $errstr);
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

// Parse JSON function
function parseJsonRequest($request) {
    $data = json_decode((string)$request, true);
    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Failed to parse JSON.');
    }
    return $data;
}

// Process /call endpoint
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] === 'application/json') {
    try {
        $requestBody = file_get_contents('php://input');
        $data = parseJsonRequest($requestBody);
        if ($data) {
            $params = $data['params'] ?? [];
            $code = $params['code'] ?? '';
            $jwt = $params['jwt'] ?? '';
            $url = sprintf("http://%s", getenv('GQL_URN') ?: '192.168.0.135:3006/gql');

            $codeFn = str_replace('function fn(', 'function func(', $code);
            $codeFn = str_replace("\\n", "\n", $codeFn);

            $executor = new CodeExecutor();
            $deepClient = new DeepClientPhpWrapper($jwt, $url);

            try {
                $resultFromFunction = $executor->execute($codeFn, $params['data'], $deepClient);
                $result = $resultFromFunction;
            } catch (ErrorException $e) {
                $logger->info("Error: ".$e->getMessage());
                $result = "Error: " . $e->getMessage();
            }

            echo $result;
        } else {
            $logger->info('Failed to parse JSON.');
            echo 'Failed to parse JSON.';
        }
    } catch (Exception $e) {
        $logger->info("An error occurred: ".$e->getMessage());
        echo "An error occurred: ".$e->getMessage();
    }
} else {
    echo '{}'; // Default response for other cases
}
?>
