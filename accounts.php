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

    const INDIRECT = true;

    if($_SERVER['HTTP_ACCEPT'] == 'application/json') {
        header('Content-Type: application/json');
        echo json_encode(jsonAccountData());
        return;
    } else {
        include 'pages/accounts_page.php';
    }

    function jsonAccountData() {

        $s = new Session();
        $s->isAuth() or header('Location: index.php?e=2');

        $user = User::find($s->user);

        $data = [
            'accounts' => []
        ];
        foreach ($user->accounts as $account) {
            array_push($data['accounts'], [
                'number' => $account->number,
                'balance' => money_format('%i', $account->balance)
            ]);
            $data['nonce'] = NonceManager::generate($user)->nonce;
        }
        return $data;
    }
