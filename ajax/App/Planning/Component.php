<?php

namespace Ajax\App\Planning;

use Jaxon\Attributes\Attribute\Before;

#[Before('checkHostAccess', ["planning", "finance"])]
abstract class Component extends \Ajax\Component
{}
