<?php
include('config.php');
include('db.php');

function error($msg) {
    error_log("Error: $msg");
    return array("error"=>$msg);
}

function input($name) {
    if (empty($_REQUEST[$name])) {
        return null;
    }

    $value = filter_var($_REQUEST[$name], FILTER_SANITIZE_STRING);
    return $value;
}

function output($data) {
    header("Content-Type: Text/Plain");
    echo json_encode($data);
    exit;
}


?>
