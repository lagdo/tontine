<?php

namespace Ajax\App\Meeting\Summary;

/**
 * @databag summary
 * @before checkHostAccess ["meeting", "sessions"]
 * @before getSession
 */
abstract class FuncComponent extends \Ajax\FuncComponent
{
    use ComponentTrait;
}
