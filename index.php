<?php
    include('common.php');
    
    // Check which page we are on.
    $url = $_SERVER['REQUEST_URI'];
    $pieces = explode('/', $url);
    $method = (!empty($pieces[2])) ? $pieces[2] : false;


    if ($method == 'login') {
        $username = input('username');
        $password = input('password');
    }else if ($method == 'create_user') {
        $username = input('username');
        $password = input('password');
        output(create_user($username, $password));
    }else if ($method == 'create_post') {
        $user = require_login();
    }else if ($method == 'delete_post') {
        $user = require_login();

        // TODO: Needs db methods

    }else if ($method == 'create_follow') {
        $user = require_login();

        $frodo = input('follow');
        if(empty($frodo)){
           fatal("Missing user to follow"); 
        }

        output(create_follow($frodo, $user['id']));

    }else if ($method == 'remove_follow') {
        $user = require_login();

        // TODO: Needs db methods

    }else if ($method == 'public_queue') {
        $offset = input('offset', false);

        output(get_queue(null, $offset));

    }else if ($method == 'user_queue') {
        $user = require_login();
        $offset = input('offset', false);
        output(get_queue($user['id'], $offset));
    }else if ($method == 'mentions') {
        $user = require_login();
        // TODO: Needs db methods
    }else if ($method == 'followers') {
        $userid = input($userid);
    }else if ($method == 'follows') {

    }


?>
