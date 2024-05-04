<?php

namespace Handy\Security;

use Handy\Context;

interface ISecurityProvider {
    static function execute(Context $ctx);
}

