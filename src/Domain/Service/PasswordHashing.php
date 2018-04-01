<?php

namespace Things\Domain\Service;

/**
 * Interface PasswordHashing
 * @package Things\Domain\Service
 */
interface PasswordHashing
{
    /**
     * @param string $plainPassword
     * @param string $hash
     * @return bool
     */
    public function verify(string $plainPassword, string $hash) : bool;

    /**
     * @param string $plainPassword
     * @return string
     */
    public function calculateHash(string $plainPassword) : string;
}
