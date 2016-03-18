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

    if ($s->authenticate($_POST['username'], $_POST['password'])) {
        header('Location: accounts.php');
    } else {
        header('Location: index.php?e=0');
    }
