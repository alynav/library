<?php

require_once '../../app/controller/BookController.php';


$server = new SoapServer(null, [
    'uri' => 'webserver/ws/book.php'
]);

// Bind class to Soap Server:
$server->setClass('BookController');

// Handle a request:
$server->handle();