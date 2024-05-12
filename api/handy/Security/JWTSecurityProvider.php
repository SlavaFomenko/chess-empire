<?php

namespace Handy\Security;

use Handy\Context;
use ReallySimpleJWT\Decode;
use ReallySimpleJWT\Jwt;
use ReallySimpleJWT\Parse;
use ReallySimpleJWT\Token;


class JWTSecurityProvider implements ISecurityProvider
{

    /**
     * @inheritDoc
     */
    static function execute(): void
    {
        $headers = Context::$request->getHeaders();

        Context::$security = new Security();

        if (!(isset($headers['Authorization']) && str_starts_with($headers['Authorization'], "Bearer "))) {
            return;
        }
        $token = str_replace('Bearer ', '', $headers['Authorization']);

        Context::$security->setToken($token);
        Context::$security->setData((object)(self::parseToken($token)));
    }

    /**
     * @inheritDoc
     */
    public static function generateToken(array $data): string
    {
        return Token::customPayload($data, $_ENV["JWT_KEY"]);
    }

    /**
     * @inheritDoc
     */
    public static function parseToken(string $token): array
    {
        $jwt = new Jwt($token);
        $parse = new Parse($jwt, new Decode());
        return $parse->parse()->getPayload();
    }

}