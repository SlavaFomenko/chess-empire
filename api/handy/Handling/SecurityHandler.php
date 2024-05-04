<?php

namespace Handy\Handling;

use Handy\Context;
use Handy\Exception\InvalidSecurityProviderException;
use Handy\Security\ISecurityProvider;


class SecurityHandler extends AbstractHandler
{

    public function handle(Context $ctx): void
    {
        if ($ctx->config->securityProvider !== null) {
            $securityProvider = $ctx->config->securityProvider;
            if (!is_subclass_of($securityProvider, ISecurityProvider::class)) {
                throw new InvalidSecurityProviderException('Security provider must implement ISecurityProvider');
            }
            call_user_func($ctx->config->securityProvider . "::execute", $ctx);
        }
    }

}