<?php

namespace Ajax\App\Meeting\Session;

/**
 * @databag meeting
 * @before checkHostAccess ["meeting", "sessions"]
 * @before getSession
 */
abstract class FuncComponent extends \Ajax\FuncComponent
{
    use ComponentTrait;
}
