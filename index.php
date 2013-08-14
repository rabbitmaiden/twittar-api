<?php
    include('common.php');
    
    // Check which page we are on.
    $url = $_SERVER['REQUEST_URI'];
    $pieces = explode('/', $url);
    $method = (!empty($pieces[2])) ? $pieces[2] : false;


    if ($method == 'login') {
        $username = input('username');
        $password = input('password');
        if (empty($username) || empty($password)) {
            fatal("Invalid username/password provided");
        }

 
    }else if ($method == 'create_user') {
        $username = input('username');
        $password = input('password');
        if (empty($username) || empty($password)) {
            fatal("Invalid username/password provided");
        }

        output(create_user($username, $password));
    }else if ($method == 'create_post') {
        $user = require_login();
    }else if ($method == 'delete_post') {
        $user = require_login();
    }else if ($method == 'create_follow') {
        $user = require_login();
    }else if ($method == 'remove_follow') {
        $user = require_login();
    }else if ($method == 'public_queue') {

    }else if ($method == 'user_queue') {
        $user = require_login();
    }else if ($method == 'mentions') {
        $user = require_login();
    }


?>
