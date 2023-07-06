<?php

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
    $url = env('GQL_URN') ? "https://" . env('GQL_URN') : "http://" . env('GQL_URN');
    $transport = new \GraphQL\Transport\GuzzleHttpGQLHTTPTransport($url, ['headers' => ['Authorization' => $token]]);
    $deep_client = new \GraphQL\Client($transport, null, null, null, true);
    return $deep_client;
}
