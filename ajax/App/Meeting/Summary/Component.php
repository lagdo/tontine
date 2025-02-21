<?php

namespace Ajax\App\Meeting\Summary;

/**
 * @databag meeting
 * @before getSession
 */
abstract class Component extends \Ajax\Component
{
    use ComponentTrait;
}
