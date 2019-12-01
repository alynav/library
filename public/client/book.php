<?php
$actions = ['createAction', 'updateAction', 'readAction', 'deleteAction', 'searchAction'];

$action = $_GET['method'];

if (in_array($action, $actions)) {

    $client = new SoapClient(null, [
        'location' => 'http://webserver/ws/book.php',
        'uri' => 'http://webserver/ws/book.php',
        'trace' => 1
    ]);

    $params = $_POST['params'];

    try {
        $result = $client->__soapCall($action, $params);
        return $result;
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}