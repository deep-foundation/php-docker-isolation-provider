<?php

namespace Tests\Feature;

use Tests\TestCase;

abstract class TestFeature extends TestCase
{
    static $with_headers = [
        'Accept' => 'application/json',
        "user-agent" => "Chrome/109.0.0.0",
        'Content-Type' => 'application/json'
    ];

    static string $description = 'Feature Test Case';
    public static function getAuthHeaders()
    {
        $headers = TestFeature::$with_headers;
        $headers['Authorization'] = 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0L3JlZ2lzdGVyIiwiaWF0IjoxNjgyMzM5ODEyLCJleHAiOjE2ODQ5Njc4MTIsIm5iZiI6MTY4MjMzOTgxMiwianRpIjoiNWo2M2E2TlIzU3JkMXBjbiIsInN1YiI6MjI0MzQzLCJwcnYiOiJmOTMwN2ViNWYyOWM3MmE5MGRiYWFlZjBlMjZmMDI2MmVkZTg2ZjU1IiwibW9kZWwiOiJBcHBcXEVudGl0aWVzXFxVc2VyIn0.xdcD_KY7ljhj5NgyF_2V47jpMDQE4pHtVrPGVxAKrwY';
        $headers['device_id'] = '123456789';
        $headers['device_name'] = 'My Device';
        return $headers;
    }
}
