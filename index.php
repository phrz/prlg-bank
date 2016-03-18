<?php

/*
INSTRUCTIONS:

index.php
   - if user does not have a valid session id
     - prompt the user to login
     - POST login creds to login.php
   - if user has a valid session id
     - redirect the user to accounts.php

*/

include_once 'class/Session.php';
include_once 'class/User.php';
include_once 'class/Account.php';

const INDIRECT = true; // direct template access prevention
$s = new Session();

if(!$s->isAuth()) {
    // Login screen
    include 'pages/login_prompt.php';
} else {
    // Accounts screen
    header("Location: accounts.php");
}


?>
