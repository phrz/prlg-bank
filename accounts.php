<?php
    /*
    INSTRUCTIONS:
    accounts.php
     - show user their account balances (fetched from database)
     - give options to withdraw money, deposit money, transfer money
    */

    include_once 'class/Session.php';
    include_once 'class/User.php';
    include_once 'class/NonceManager.php';
    include_once 'class/Nonce.php';

    $s = new Session();
    $s->isAuth() or header('Location: index.php?e=2');

    $user = User::find( $s->user );

    const INDIRECT = true;
    include 'pages/accounts_page.php';
?>
