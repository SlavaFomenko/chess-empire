<?php

namespace Handy\Security;

interface ISecurityProvider
{

    /**
     * @return void
     */
    public static function execute(): void;


    /**
     * @param array $data
     * @param int $exp
     * @return string
     */
    public static function generateToken(array $data, int $exp): string;

    /**
     * @param string $token
     * @return array
     */
    public static function parseToken(string $token): array;

    /**
     * @param string $token
     * @return bool
     */
    public static function validateToken(string $token): bool;

}

