<?php

require_once '../../app/controller/PersonController.php';


$server = new SoapServer(null, [
    'uri' => 'webserver/ws/person.php'
]);

// Bind class to Soap Server:
$server->setClass('PersonController');

// Handle a request:
$server->handle();