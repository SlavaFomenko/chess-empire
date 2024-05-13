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
     * @inheritDoc
     */
    public static function generateToken(array $data): string
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

}