<?php

namespace Ajax\App\Meeting\Session;

/**
 * @databag meeting
 * @before checkHostAccess ["meeting", "sessions"]
 * @before getSession
 */
abstract class PageComponent extends \Ajax\PageComponent
{
    use ComponentTrait;
}
