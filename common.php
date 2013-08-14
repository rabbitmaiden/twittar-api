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

function fatal($msg){
    return output(error($msg));
}

function is_logged_in() {
    $userid = input('userid');
    $token = input('token');
    if(empty($userid) || empty($token)){
        return false;
    }

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
