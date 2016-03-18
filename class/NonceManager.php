<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include_once 'Nonce.php';
    include_once 'Session.php';

    class NonceManager
    {
        // Factory function for an Nonce.
        public static function generate(User $user = null): Nonce
        {

            // Only generate an owned Nonce for the authenticated
            // user.
            if (isset($user)) {
                $s = new Session();
                if (!$s->isAuth() || $s->user != $user->username) {
                    throw new Exception('Owned nonce with owner '.$user->username.' requested by unauthorised party!', 1);
                }
            }

            $unique = openssl_random_pseudo_bytes(64);
            $unique = bin2hex($unique);

            $n = Nonce::create([
                'nonce' => $unique,
                'user' => $user,
            ]);

            $n->save();

            return $n;
        } // generate

        // Validate then the Nonce
        public static function consume(string $givenNonce, string $givenUsername = null, Nonce &$knownNonce = null): bool
        {
            // Validation
            // 1. attempt to retrieve the Nonce model with matching token value
            try {
                $knownNonce = Nonce::findAndDelete($givenNonce);
            } catch (Exception $e) {
                // This nonce does not exist!
                // 99% of the time, this isn't forgery,
                // but a double-click on a form submit where
                // the first request consumes the Nonce and
                // the second can't find the now-gone nonce.
                error_log($e->getMessage());
                error_log('Nonce not found in DB');

                return false;
            }

            // 2. Has the Nonce expired?
            $expiryInterval = new DateInterval(Config::$nonce['expiry']);
            $expiry = $knownNonce->timestamp->add($expiryInterval);

            $now = new DateTime('now', new DateTimeZone(Config::$database['timezone']));
            $expired = $expiry < $now;

            if ($expired) {
                error_log('Expiry: '.$expiry->format('Y-m-d H:i:sP'));
                error_log('Current Time: '.$now->format('Y-m-d H:i:sP'));
                error_log('Nonce found but expired');

                return false;
            }

            // 3. if the Nonce is owned, confirm the owner
            try {
                $owner = $knownNonce->user;
                // if the owner names match, it's valid.
                if ($owner->username == $givenUsername) {
                    error_log('Nonce owner matches given user');

                    return true;
                } else {
                    error_log("Nonce owner $owner doesn't match given user $givenUsername");
                }
            } catch (Exception $e) {
                // this Nonce has no owner
                error_log('No owner, checking null...');

                return !isset($givenUsername);
            }
        } // end consume
    } // end NonceManager
;
