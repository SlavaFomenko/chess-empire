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
     * @param Context $ctx
     * @return void
     * @throws \ReallySimpleJWT\Exception\JwtException
     */
    static function execute(Context $ctx): void
    {

        $headers = $ctx->request->getHeaders();
        $security = new Security();
        if (!(isset($headers['Authorization']) && str_starts_with($headers['Authorization'], "Bearer "))) {
            return;
        }
        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $security->setToken($token);

        $jwt = new Jwt($token);
        $parse = new Parse($jwt, new Decode());
        $parsed = $parse->parse();

        $security->setData((object)($parsed->getPayload()));

        $ctx->security = $security;

    }

    /**
     * @param array $data
     * @return string
     */
    public static function generateToken(array $data): string
    {
        return Token::customPayload($data, $_ENV["JWT_KEY"]);
    }

}