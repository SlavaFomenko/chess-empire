<?php

namespace Handy\Handling;

use Handy\Context;
use Handy\Exception\InvalidSecurityProviderException;
use Handy\Security\ISecurityProvider;


class SecurityHandler extends AbstractHandler
{

    public function handle(): void
    {
        if (Context::$config->securityProvider !== null) {
            $securityProvider = Context::$config->securityProvider;
            if (!is_subclass_of($securityProvider, ISecurityProvider::class)) {
                throw new InvalidSecurityProviderException('Security provider must implement ISecurityProvider');
            }
            call_user_func(Context::$config->securityProvider . "::execute");
        }
        parent::handle();
    }

}