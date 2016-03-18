<?php
    include_once 'class/Session.php';

    $s = new Session();

    $s->destroy();
    $s = null;

    header('Location: index.php?e=1');
