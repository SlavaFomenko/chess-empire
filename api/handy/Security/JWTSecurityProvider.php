<?php

namespace Handy\Security;

use Handy\Context;
use ReallySimpleJWT\Decode;
use ReallySimpleJWT\Exception\JwtException;
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

        if(!self::validateToken($token)){
            return;
        }

        $payload = self::parseToken($token);

        Context::$security->setToken($token);
        Context::$security->setRole($payload["role"]);
        Context::$security->setData((object)($payload));
    }


    /**
     * @param array $data
     * @param int $exp
     * @return string
     */
    public static function generateToken(array $data, int $exp): string
    {
        $now = time();
        $data['iat'] = $now;
        $data['exp'] = $now + $exp;

        return Token::customPayload($data, $_ENV["JWT_KEY"]);
    }

    /**
     * @inheritDoc
     * @throws JwtException
     */
    public static function parseToken(string $token): array
    {
        $jwt = new Jwt($token);
        $parse = new Parse($jwt, new Decode());
        return $parse->parse()->getPayload();
    }

    public static function validateToken(string $token): bool
    {
        return Token::validate($token, $_ENV["JWT_KEY"]) && Token::validateExpiration($token);
    }

}