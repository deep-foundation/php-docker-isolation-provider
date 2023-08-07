<?php

namespace Tests\Feature;

use Tests\Feature\TestFeature;


class CallTest extends TestFeature
{
    public function test()
    {
        $options = [
            "params"=>[
                "code"=>"function fn(\$deep)\\n{\\n  return 1;\\n}",
                "container"=>[
                    "name"=>"deep-f42519240f4bef4fc6249434e79f4b7f",
                    "host"=>"localhost",
                    "port"=>40755,
                    "options"=>[
                        "publish"=>true,
                        "forceRestart"=>true,
                        "handler"=>"deepf/php-docker-isolation-provider:dev",
                        "code"=>"function fn(\$deep)\\n{\\n  return 1;\\n}",
                        "jwt"=>env('BEARER_TOKEN', ''),
                        "data"=>[
                            "triggeredByLinkId"=>380,
                            "oldLink"=>null,
                            "newLink"=>[
                                "from_id"=>0,
                                "type_id"=>1137,
                                "id"=>1155,
                                "to_id"=>0,
                                "value"=>null
                            ],
                            "promiseId"=>1156
                        ]
                    ]
                ],
                "jwt"=>env('BEARER_TOKEN', ''),
                "data"=>[
                    "triggeredByLinkId"=>380,
                    "oldLink"=>null,
                    "newLink"=>[
                        "from_id"=>0,
                        "type_id"=>1137,
                        "id"=>1170,
                        "to_id"=>0,
                        "value"=>null
                    ],
                    "promiseId"=>1171
                ]
            ]
        ];

        $response = $this->withHeaders(parent::$with_headers)->json('POST', '/call', $options);

        var_dump($response->json());

        $response->assertStatus(200);
    }
}

//{"params":{"code":"function fn(deep):\\n{\\n  return 1;\\n}","container":{"name":"deep-f42519240f4bef4fc6249434e79f4b7f","host":"localhost","port":40755,"options":{"publish":true,"forceRestart":true,"handler":"deepf/php-docker-isolation-provider:dev","code":"function fn(deep):\\n{\\n  return 1;\\n}","jwt":"eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJodHRwczovL2hhc3VyYS5pby9qd3QvY2xhaW1zIjp7IngtaGFzdXJhLWFsbG93ZWQtcm9sZXMiOlsiYWRtaW4iXSwieC1oYXN1cmEtZGVmYXVsdC1yb2xlIjoiYWRtaW4iLCJ4LWhhc3VyYS11c2VyLWlkIjoiMzgwIn0sImlhdCI6MTY5MTAyMjIyMH0.RytIitS3AtKlVb-RWb9T-Aj2lvd9mste5FuaL9OORA8","data":{"triggeredByLinkId":380,"oldLink":null,"newLink":{"from_id":0,"type_id":1137,"id":1155,"to_id":0,"value":null},"promiseId":1156}}},"jwt":"eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJodHRwczovL2hhc3VyYS5pby9qd3QvY2xhaW1zIjp7IngtaGFzdXJhLWFsbG93ZWQtcm9sZXMiOlsiYWRtaW4iXSwieC1oYXN1cmEtZGVmYXVsdC1yb2xlIjoiYWRtaW4iLCJ4LWhhc3VyYS11c2VyLWlkIjoiMzgwIn0sImlhdCI6MTY5MTAyMjg3Mn0.Zwy2piBMdtpiJpweFRyL2p6q8z3mSUKEy7szVKN0U_I","data":{"triggeredByLinkId":380,"oldLink":null,"newLink":{"from_id":0,"type_id":1137,"id":1170,"to_id":0,"value":null},"promiseId":1171}}}
