<?php

namespace Ajax\App\Meeting\Summary;

/**
 * @databag summary
 * @before checkHostAccess ["meeting", "sessions"]
 * @before getSession
 */
abstract class Component extends \Ajax\Component
{
    use ComponentTrait;
}
