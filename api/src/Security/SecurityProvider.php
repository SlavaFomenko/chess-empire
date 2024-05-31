<?php

namespace App\Security;

use App\Entity\User;
use Handy\Context;
use Handy\Security\JWTSecurityProvider;
use Handy\Security\Security;

class SecurityProvider extends JWTSecurityProvider
{
    /**
     * @inheritDoc
     */
    static function execute(): void
    {
        parent::execute();
        if(!Context::$security->getToken()){
            return;
        }

        $repo = Context::$entityManager->getRepository(User::class);
        $id = Context::$security->getData()->id;
        $user = $repo->find($id);

        if(!$user){
            Context::$security = new Security();
            return;
        }

        Context::$security->setRole($user->getRole());
    }
}