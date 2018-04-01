<?php

namespace Things\Infrastructure\Domain\Service;

use Things\Domain\Service\PasswordHashing;

/**
 * Class Md5PasswordHashing
 * @package Things\Infrastructure\Domain\Service
 */
class Md5PasswordHashing implements PasswordHashing
{
    /**
     * @var string
     */
    protected $salt;

    /**
     * Md5PasswordHashing constructor.
     * @param string $salt
     */
    public function __construct(string $salt)
    {
        $this->salt = $salt;
    }

    /**
     * @param string $plainPassword
     * @param string $hash
     * @return bool
     */
    public function verify(string $plainPassword, string $hash) : bool
    {
        return $hash === $this->calculateHash($plainPassword);
    }

    /**
     * @param string $plainPassword
     * @return string
     */
    public function calculateHash(string $plainPassword) : string
    {
        return md5($plainPassword . '_' . $this->salt());
    }

    /**
     * @return string
     */
    private function salt()
    {
        return md5($this->salt);
    }
}
