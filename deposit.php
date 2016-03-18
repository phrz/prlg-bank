<?php
    /*
    INSTRUCTIONS:
    deposit.php
     - POST request to add money to an account
     - validates session ID belongs to account
     - updates database accordingly
    */

    include_once 'class/Form.php';
    include_once 'class/Account.php';
    include_once 'class/Session.php';

    $s = new Session();
    $s->isAuth() or header('Location: index.php?e=2');

    $form = new Form('POST');
    $form->addField('amount', 'Form::isMoney');
    $form->addField('toAccount', 'Form::validateAccount');
    $form->addField('nonce', 'Form::validateNonce');

    $form->submit([
        'success' => function($data) {

            // get the Account Model
            $number = $data['toAccount'];
            $account = Account::find($number);

            // Add the requested amount
            $deposit = floatval( $data['amount'] );
            $account->balance += $deposit;

            // Commit the Model back to the DB
            $account->save();

            header("Location: accounts.php?e=d0");
        },
        'failure' => function($data) {
            // Redirect back to accounts
            header("Location: accounts.php?e=d1");
        }
    ]);
?>
