<?php

require_once '../../app/controller/BorrowController.php';


$server = new SoapServer(null, [
    'uri' => 'webserver/ws/borrow.php'
]);

// Bind class to Soap Server:
$server->setClass('BorrowController');

// Handle a request:
$server->handle();