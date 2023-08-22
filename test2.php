<?php

extension_loaded('deep_client_php_extension') or dl('deep_client_php_extension.so');

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
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
}

$executor = new CodeExecutor();
$deepClient = new DeepClientPhpWrapper(
	'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJodHRwczovL2hhc3VyYS5pby9qd3QvY2xhaW1zIjp7IngtaGFzdXJhLWFsbG93ZWQtcm9sZXMiOlsiYWRtaW4iXSwieC1oYXN1cmEtZGVmYXVsdC1yb2xlIjoiYWRtaW4iLCJ4LWhhc3VyYS11c2VyLWlkIjoiMzgwIn0sImlhdCI6MTY5MTkxMTQxM30.W0GOuqOvRZrgrVZkLaceKTPBitXwR-1WlxLgxUZXOnY',
	'http://192.168.0.135:3006/gql'
);

$functionCode = '
    function func($data, $deep) {
        return $deep->select(90);
    };';

$data = [];


try {
    $resultFromFunction = $executor->execute($functionCode, $data, $deepClient);
    echo "Result from CodeExecutor: " . $resultFromFunction;
} catch (ErrorException $e) {
    echo "Error: " . $e->getMessage();
}



