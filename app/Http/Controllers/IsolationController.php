<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use GraphQL\Client;
use GraphQL\QueryBuilder\QueryBuilder;
use GraphQL\Transport\GuzzleHttpGQLHTTPTransport;

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
        return new JsonResponse([]);
    }

    public function initialization(): JsonResponse
    {
        return new JsonResponse([]);
    }

    public function call(Request $request)
    {
        try {
            $body = json_decode($request->getContent(), true);
            $params = $body['params'];
            $args = [
                'deep' => $this->make_deep_client($params['jwt']),
                'data' => $params['data'],
                'gql' => new QueryBuilder(),
            ];
            $result = $this->execute_handler($params['code'], $args);
            return new JsonResponse(['resolved' => $result]);
        } catch (\Exception $e) {
            return new JsonResponse(['rejected' => $e->getTraceAsString()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
