<?php

namespace Ajax\App\Meeting\Session;

/**
 * @databag meeting
 * @before checkHostAccess ["meeting", "sessions"]
 * @before getSession
 */
abstract class Component extends \Ajax\Component
{
    use ComponentTrait;
}
