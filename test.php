<?php
// Lame smoke tests


include('db.php');

$user = array(
    "username"=>"alice".mt_rand(0,1000),
    "password"=>"blah",
);

$user = create_user($user['username'], $user['password']);
var_dump($user);

$result = validate_token($user['id'], $user['token']);
var_dump($result);

$body = "FIRST!!!!111";

$result = create_post($user['id'], $body, 0);

var_dump($result);
