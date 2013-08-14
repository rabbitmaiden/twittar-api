<?php
    include('common.php');
    
    // Check which page we are on.
    $url = $_SERVER['REQUEST_URI'];
    $pieces = explode('/', $url);
    $method = (!empty($pieces[2])) ? $pieces[2] : false;


    if ($method == 'login') {

    }else if ($method == 'create_user') {

    }


?>
