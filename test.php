<?php
// Lame smoke tests
include('common.php');

$user = array(
    "username"=>"alice".mt_rand(0,1000),
    "password"=>"blah",
);

$user = create_user($user['username'], $user['password']);
var_dump($user);

$result = validate_token($user['id'], $user['token']);
echo "Validate token: ";
var_dump($result);

$follow = create_follow('1', $user['id']);

echo "create_follow: ";
var_dump($follow);

$follow = create_follow($user['id'], 1);

$body = "FIRST!!!!111";

$result = create_post($user['id'], $body, 0);

var_dump($result);


