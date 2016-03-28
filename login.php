<?php

    /*
    INSTRUCTIONS:
    login.php
     - POST request accepts username and password,
       hashes password with salt, and queries database for
       valid username/hashed password combination.
     - transfers user to accounts.php on successful login
     - transfers user back to index.php on unsuccessful login
     */

    include_once 'class/User.php';
    include_once 'class/Session.php';

    $s = new Session();

    // handle JSON and return JSON
    if($_SERVER['HTTP_ACCEPT'] == 'application/json') {
        $bodyRaw = file_get_contents("php://input");
        $body = json_decode($bodyRaw, true);
        
        if($s->authenticate($body['username'] ?? null, $body['password'] ?? null)) {
            http_response_code(200);
            return;
        } else {
            http_response_code(401);
            return;
        }
    }

    if ($s->authenticate($_POST['username'] ?? null, $_POST['password'] ?? null)) {
        header('Location: accounts.php');
    } else {
        header('Location: index.php?e=0');
    }
