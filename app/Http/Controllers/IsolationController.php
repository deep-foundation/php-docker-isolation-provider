<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\JsonResponse;
//use GraphQL\Client;
//use GraphQL\QueryBuilder\QueryBuilder;
//use GraphQL\Transport\GuzzleHttpGQLHTTPTransport;

class IsolationController extends Controller
{
    private mixed $GQL_URN;
    private bool $GQL_SSL;

    public function __construct()
    {
        $this->GQL_URN = env('GQL_URN', 'localhost:3006/gql');
        $this->GQL_SSL = (bool) env('GQL_SSL', 0);
    }

    public function healthz(): JsonResponse
    {
        return new JsonResponse();
    }

    public function initialization(): JsonResponse
    {
        return new JsonResponse();
    }

    public function call(Request $request)
    {
        Log::channel('deep')->error(
            json_encode([
                'MESSAGE' => 'CALL.' . __METHOD__
            ]),
            [$request->all()]
        );

        try {
            $body = json_decode($request->getContent(), true);
            $params = $body['params'];
            $args = [
                'deep' => $this->makeDeepClient($params['jwt']),
                'data' => $params['data'],
                'gql' => new QueryBuilder(),
            ];
            $codeFn = str_replace('function fn(', 'function func(', $params['code']);
            $codeFn = str_replace("\\n", "\n", $codeFn);
            eval($codeFn);
            $result = func($args);
            return new JsonResponse(['resolved' => $result]);
        } catch (\Exception $e) {
            return new JsonResponse(['rejected' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function makeDeepClient(string $jwt): string
    {
        return $jwt;
    }
}
