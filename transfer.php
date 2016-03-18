<?php
    /*
    INSTRUCTIONS
    transfer.php
     - POST request to transfer from one account to another account
     - validates session ID belongs to both accounts
     - validates enough money in source account and updates database accordingly
    */

    include_once 'class/Form.php';
    include_once 'class/Account.php';
    include_once 'class/Session.php';

    $s = new Session();
    $s->isAuth() or header('Location: index.php?e=2');

    $form = new Form('POST');
    $form->addField('amount', 'Form::isMoney');
    $form->addField('fromAccount', 'Form::validateAccount');
    $form->addField('toAccount', 'Form::validateAccount');
    $form->addField('nonce', 'Form::validateNonce');

    // Validate accounts and field values, then perform
    // logical validation in callback below.
    $form->submit([
        'success' => function ($data) {

            // get the source Account Model
            $fromNo = $data['fromAccount'];
            $fromAccount = Account::find($fromNo);

            // get the destination Account Model
            $toNo = $data['toAccount'];
            $toAccount = Account::find($toNo);

            $transfer = floatval($data['amount']);

            // Sufficient funds check
            if ($transfer > $fromAccount->balance) {
                header('Location: accounts.php?e=t2');
            }

            // Different From/To accounts check
            elseif ($fromNo == $toNo) {
                header('Location: accounts.php?e=t3');
            } else {
                // Perform the transfer
                $fromAccount->balance -= $transfer;
                $toAccount->balance += $transfer;

                // Commit the Model back to the DB
                $fromAccount->save();
                $toAccount->save();

                header('Location: accounts.php?e=t0');
            }
        },
        'failure' => function ($data) {
            // Redirect back to accounts
            header('Location: accounts.php?e=t1');
        },
    ]);
