<?php

include('db.php');

$user = array(
    "username"=>"alice".mt_rand(0,1000),
    "password"=>"blah",
);

$user = create_user($user['username'], $user['password']);
var_dump($user);

$result = validate_token($user['id'], $user['token']);
var_dump($result);

$post = array(
    

);
