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
     * @return string
     */
    public static function generateToken(array $data): string;

    /**
     * @param string $token
     * @return array
     */
    public static function parseToken(string $token): array;

}

