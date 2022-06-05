<?php

namespace App\Ajax;

use Siak\Tontine\Model\Tontine;
use Siak\Tontine\Model\User;

use Jaxon\App\CallableClass as JaxonCallableClass;

class CallableClass extends JaxonCallableClass
{
    /**
     * @var User|null
     */
    public ?User $user;

    /**
     * @var Tontine|null
     */
    public ?Tontine $tontine;
}
