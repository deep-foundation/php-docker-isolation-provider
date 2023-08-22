<?php

extension_loaded('deep_client_php_extension') or dl('deep_client_php_extension.so');

$client = new DeepClientPhpWrapper(
	'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJodHRwczovL2hhc3VyYS5pby9qd3QvY2xhaW1zIjp7IngtaGFzdXJhLWFsbG93ZWQtcm9sZXMiOlsiYWRtaW4iXSwieC1oYXN1cmEtZGVmYXVsdC1yb2xlIjoiYWRtaW4iLCJ4LWhhc3VyYS11c2VyLWlkIjoiMzgwIn0sImlhdCI6MTY5MTkxMTQxM30.W0GOuqOvRZrgrVZkLaceKTPBitXwR-1WlxLgxUZXOnY',
	'http://192.168.0.135:3006/gql'
);
var_dump($client);

$new_record = array(
    "type_id" => 58,
    "from_id" => 0,
    "to_id" => 0
);
var_dump($client->insert($new_record));

//var_dump($client->insert($new_record));
//var_dump($client->insert("sql query here"));

?>