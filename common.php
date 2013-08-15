<?php
include('config.php');
include('db.php');

function error($msg) {
    error_log("Error: $msg");
    return array("error"=>$msg);
}

function input($name, $required = true) {
    if (empty($_REQUEST[$name])) {
        if($required) {
            fatal("Missing input parameter: $name");
        } else {
            return null;
        }
    }

    $value = filter_var($_REQUEST[$name], FILTER_SANITIZE_STRING);
    return $value;
}

function output($data) {
    header("Content-Type: Text/Plain");
    echo json_encode($data);
    exit;
}

function fatal($msg){
    output(error($msg));
}

function is_logged_in() {
    $userid = input('userid');
    $token = input('token');
    return validate_token($userid, $token);
}

function require_login() {
    if(!is_logged_in()){
        fatal("This action requires login");
    }

    $userid = input('userid');
    return get_user($userid);
}


?>
