<?php

namespace Handy\Security;

use Handy\Context;
use ReallySimpleJWT\Decode;
use ReallySimpleJWT\Jwt;
use ReallySimpleJWT\Parse;
use ReallySimpleJWT\Token;


class EmptySecurityProvider implements ISecurityProvider
{

    /**
     * @inheritDoc
     */
    static function execute(): void
    {
        Context::$security = new Security();
    }


    /**
     * @param array $data
     * @param int $exp
     * @return string
     */
    public static function generateToken(array $data, int $exp): string
    {
        return "";
    }

    /**
     * @inheritDoc
     */
    public static function parseToken(string $token): array
    {
        return [];
    }

    public static function validateToken(string $token): bool
    {
        return true;
    }

}