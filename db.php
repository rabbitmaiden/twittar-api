<?php

include('config.php');

function db() {
    global $db;
    if(empty($db)){
        $db = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
    }
    return $db;
}

function error($msg) {
    error_log("Error: $msg");
    return array("error"=>$msg);
}

function create_user($username, $password, $icon = null) {
    $db = db();

    $password = sha1($password);

    $query = "SELECT 1 FROM user WHERE username = '".mysqli_real_escape_string($db, $username)."'";
    $result = mysqli_query($db, $query);
    if (mysqli_num_rows($result)) {
        return error("Username is already taken"); 
    }

    $query = "INSERT INTO user (username, password, icon) VALUES
                ('".mysqli_real_escape_string($db, $username)."',
                 '".mysqli_real_escape_string($db, $password)."',
                 '".mysqli_real_escape_string($db, $icon)."')";

    $result = mysqli_query($db, $query);
    if(mysqli_affected_rows($db) != 1){
        error_log("Create user failed: ".mysqli_error($db));
        return error("Create User failed");
    }
    
    $userid = mysqli_insert_id($db);
    
    $token = generate_token($userid, $password);

    return array('id'=>$userid, 'token'=>$token);
}


function generate_token($id, $pass) {
    $token = sha1($id.$pass.SECRET);
    return $token;
}


function validate_token($id, $token) {
    $db = db();
    $query = "SELECT password FROM user WHERE id = '".mysqli_real_escape_string($db, $id)."'";
    $result = mysqli_query($db, $query);
    if(mysqli_num_rows($result) == 1){
        $password = mysqli_fetch_row($result)[0];
        $validtoken = generate_token($id, $password);
        if ($token === $validtoken) {
            return true;
        }
    }
    return error("Invalid Token");
}


function create_post($author, $body, $private = 0, $replyto = null, $replyparent = null) {
    $db = db();

    $query = "INSERT INTO messages (author, body, private, replyto, replyparent) VALUES
        ('".mysqli_real_escape_string($db, $author)."',
         '".mysqli_real_escape_string($db, $body)."',
         '".mysqli_real_escape_string($db, $private)."',
         '".mysqli_real_escape_string($db, $replyto)."',
         '".mysqli_real_escape_string($db, $replyparent)."')";
    $result = mysqli_query($db, $query) or error_log(mysqli_error($db));

    if (mysqli_affected_rows($db)!= 1){
        error_log("Create post failed: ".mysqli_error($db));
        return error("Create post failed!");
    }    

    $id = mysqli_insert_id($db);

    // TODO: Insert into follower queues

    // TODO: Insert into public queue

    return $id;

}




?>
