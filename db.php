<?php

include('config.php');

function db() {
    global $db;
    if(empty($db)){
        $db = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
    }
    return $db;
}

function create_user($username, $password, $icon = null) {
    $db = db();

    $password = sha1


    $query = "INSERT INTO user (username, password, icon) VALUES
                ('".mysqli_real_escape_string($username)."',
                 '".mysqli_real_escape_string($password)."',
                 '".mysqli_real_escape_string($icon)."')";

    $result = mysqli_query($query, $db);

}


function create_post($author, $body, $private = 0, $replyto = null, $replyparent = null) {
    $db = db();

    $query = "INSERT INTO messages (author, body, private, replyto, replyparent) VALUES
        ('".mysqli_real_escape_string($author)."',
         '".mysqli_real_escape_string($body)."',
         '".mysqli_real_escape_string($private)."',
         '".mysqli_real_escape_string($replyto)."',
         '".mysqli_real_escape_string($replyparent)."')";
    $result = mysqli_query($query, $db) or error_log(mysqli_error($db));

    // TODO: Insert into follower queues

    // TODO: Insert into public queue
}




?>
