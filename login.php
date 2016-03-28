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

    if ($s->authenticate($_POST['username'] ?? null, $_POST['password'] ?? null)) {
        if($_SERVER['HTTP_ACCEPT'] == 'application/json') {
            http_response_code(200); // OK
        } else {
            header('Location: accounts.php');
        }
    } else {
        if($_SERVER['HTTP_ACCEPT'] == 'application/json') {
            http_response_code(401); // Unauthorized
        } else {
            header('Location: index.php?e=0');
        }
    }
