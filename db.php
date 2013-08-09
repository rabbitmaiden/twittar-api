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

function create_follow($frodo, $samwise) {
    $db = db();
    $query = "INSERT INTO follows VALUES (".
            "'".mysqli_real_escape_string($db, $frodo)."',".
            "'".mysqli_real_escape_string($db, $samwise)."')";

    $result = mysqli_query($db, $query);
    if(mysqli_affected_rows($db)!=1){
        error_log("Create follow failed: ".mysqli_error($db));
        return error("Create follow failed!");
    }
    return true;
}

// FIXME: remove_follow


function create_post($author, $body, $private = 0, $replyto = null) {
    $db = db();

    $replyparent = null;
    if(!empty($replyto)){
        $query = "SELECT replyparent FROM messages WHERE id='".mysqli_real_escape_string($db, $replyto)."'";
        $result = mysqli_query($db, $query);
        if(mysqli_num_rows($result)==1){
            $val = mysqli_fetch_row($result)[0];
            if(!empty($val)){
                $replyparent = $val;
            }else{
                $replyparent = $replyto;
            }
        }
    }

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


    // FIXME: Mentions


    if (empty($replyto) && empty($private)){
        // Insert into own queue
        $query = "INSERT INTO queue (owner, message) VALUES (
            '".mysqli_real_escape_string($db, $author)."',
            '".mysqli_real_escape_string($db, $id)."')";
        mysqli_query($db, $query);

        $followers = get_followers($author);
        foreach($followers as $samwise){
            $inquery = "INSERT INTO queue (owner, message) VALUES (
                '".mysqli_real_escape_string($db, $samwise)."',
                '".mysqli_real_escape_string($db, $id)."')";
            mysqli_query($db, $inquery);
        }

        $query = "INSERT INTO publicqueue VALUES ('".mysqli_real_escape_string($db, $id)."')";
        $result = mysqli_query($db, $query);        

    }
    return $id;

}

//FIXME: remove_post


$GLOBALS['user_cache'] = array();
function get_user($id) {
    if(!empty($GLOBALS['user_cache'][$id])){
        return $GLOBALS['user_cache'][$id];
    }

    // Redis here?

    $db = db();
    $query = "SELECT id, username, icon FROM user WHERE id='".mysqli_real_escape_string($db, $id)."'";
    $result = mysqli_query($db, $query);
    if(mysqli_num_rows($result)!=1){
        return false;
    }
    $user = mysqli_fetch_assoc($result);
    $GLOBALS['user_cache'][$id] = $user;
    return $user;
}

function get_followers($frodo) {
    $db = db();
    $query = "SELECT samwise FROM follows WHERE frodo='".mysqli_real_escape_string($db, $frodo)."'";
    $result = mysqli_query($db, $query);
    if(empty($result)){
        return array();
    }
    $followers = array();
    while($row = mysqli_fetch_row($result)){
        $followers[] = $row[0];
    }
    return $followers;
}

function get_post($id) {
    $db = db();
    $query = "SELECT * FROM messages WHERE id='".mysqli_real_escape_string($db, $id)."'";
    $result = mysqli_query($db, $query);
    if(mysqli_num_rows($result)!=1){
        return false;
    }
    $post = mysqli_fetch_assoc($result);
    $post['author'] = get_user($post['author']);

    // Get replies
    $query = "SELECT id FROM messages WHERE replyto='".mysqli_real_escape_string($db, $id)."'";
    $result = mysqli_query($db, $query);
    if(mysqli_num_rows($result)>0){
        $post['replies'] = array();
        while($row = mysqli_fetch_row($result)){
            $replyid = $row[0];
            $post['replies'][] = get_post($replyid);
        }
    }
    return $post;
}

function get_queue($id = null, $offset = 0){
    $db = db();

    if(empty($id)) {
        $query = "SELECT message FROM publicqueue ";
    } else {
        $query = "SELECT message FROM queue WHERE owner='".mysqli_real_escape_string($db, $id)."' ";
    }

    $offset = intval($offset);
    if(!empty($offset) && $offset > 0 ){
        $query .= ' OFFSET '.mysqli_real_escape_string($db, $offset);
    }
    
    $query .= " ORDER BY message DESC";

    $result = mysqli_query($db, $query) or error_log(mysqli_error($db));
    if(empty($result)){
        return error("Could not load queue");
    }

    $posts = array();
    while ($row = mysqli_fetch_row($result)) {
        $posts[] = get_post($row[0]);
    }

    return $posts;
}

?>
