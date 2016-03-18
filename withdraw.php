<?php
    /*
    INSTRUCTIONS:
    withdraw.php
     - POST request to subtract money from account
    */

    include_once 'class/Form.php';
    include_once 'class/Account.php';
    include_once 'class/Session.php';

    $s = new Session();
    $s->isAuth() or header('Location: index.php?e=2');

    $form = new Form('POST');
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
                header('Location: accounts.php?e=w2');
            } else {
                $account->balance -= $withdraw;

                // Commit the Model back to the DB
                $account->save();

                header('Location: accounts.php?e=w0');
            }
        },
        'failure' => function ($data) {
            // Redirect back to accounts
            header('Location: accounts.php?e=w1');
        },
    ]);
