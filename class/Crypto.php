<?php

// Model object for a hashed password.
// Stores the implementation of the hashing
// algorithm and never stores the plaintext
// after object construction.

class Crypto
{
    private $salt;
    private $hash;

    // Generate a salt and store the hash
    public function __construct($plain, $salt = false, $compute = true)
    {
        if (!$salt && $compute) {
            $this->salt = $this->generateSalt();
        } else {
            $this->salt = $salt;
        }

        if ($compute) {
            $this->hash = $this->computeHash($plain);
        }
    }

    // withHash($hash: string, $salt: string) -> Crypto
    public static function withHash($hash, $salt)
    {
        $c = new self(null, null, false);
        $c->setHash($hash);
        $c->setSalt($salt);

        return $c;
    }

    // compare($aC: Crypto, $bC: Crypto) -> bool
    public static function compare($aC, $bC)
    {
        // Get the two underlying hashes
        $a = $aC->getHash();
        $b = $bC->getHash();

        // perform a time attack resistant comparison
        $ret = strlen($a) ^ strlen($b);
        $ret |= array_sum(unpack('C*', $a ^ $b));

        return !$ret;
    }

    // generateSalt() -> string
    private function generateSalt()
    {
        // mt_rand() is inherently not
        // crypto secure, but for lack of
        // OpenSSL on this server, this unique
        // yet predictable salt is considered
        // sufficient.

        $salt = openssl_random_pseudo_bytes(32);
        $salt = bin2hex($salt);

        return $salt;
    }

    // computeHash($plain: string) -> string
    private function computeHash($plain)
    {
        // Iteratively apply the SHA 512
        // hash on the password, appending
        // both the salt and the original
        // password to prevent collision.

        $iterations = 100;

        $result = hash('sha512', $plain.$this->salt);

        for ($i = 0; $i < $iterations; ++$i) {
            $result = hash('sha512', $result.$this->salt.$plain);
        }

        return $result;
    } // end computeHash

    public function getHash()
    {
        return $this->hash;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function setHash($h)
    {
        $this->hash = $h;
    }

    public function setSalt($s)
    {
        $this->salt = $s;
    }
} // end Crypto
;
