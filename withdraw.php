<?php
    /*
    INSTRUCTIONS:
    withdraw.php
     - POST request to subtract money from account
    */

    include_once 'class/Form.php';
    include_once 'class/Account.php';
    include_once 'class/Session.php';

    $jsonMode = ($_SERVER['HTTP_ACCEPT'] == 'application/json');

    $s = new Session();

    if(!$s->isAuth()) {
        $jsonMode ? http_response_code(401) : header('Location: index.php?e=2');
        return;
    }

    if($jsonMode) {
        $form = new Form('JSON');
    } else {
        $form = new Form('POST');
    }

    $form->addField('amount', 'Form::isMoney');
    $form->addField('fromAccount', 'Form::validateAccount');
    $form->addField('nonce', 'Form::validateNonce');

    $form->submit([
        'success' => function ($data) {

            // get the Account Model
            $number = $data['fromAccount'];
            $account = Account::find($number);

            // If the withdrawal amount is more than the
            // balance, redirect with `w2` (insuff. funds)
            $withdraw = floatval($data['amount']);

            if ($withdraw > $account->balance) {
                // Insufficient funds
                if($_SERVER['HTTP_ACCEPT'] == 'application/json') {
                    header('Content-Type: application/json');
                    http_response_code(409); // Conflict
                } else {
                    header('Location: accounts.php?e=w2');
                }
            } else {
                // Sufficient funds
                $account->balance -= $withdraw;

                // Commit the Model back to the DB
                $account->save();

                if($_SERVER['HTTP_ACCEPT'] == 'application/json') {
                    header('Content-Type: application/json');
                    http_response_code(200); // OK
                } else {
                    header('Location: accounts.php?e=w0');
                }
            }
        },
        'failure' => function ($data) {
            if($_SERVER['HTTP_ACCEPT'] == 'application/json') {
                header('Content-Type: application/json');
                http_response_code(400); // Bad Request
            } else {
                header('Location: accounts.php?e=w1');
            }
        },
    ]);
