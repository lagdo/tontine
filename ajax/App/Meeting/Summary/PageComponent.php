<?php

namespace Ajax\App\Meeting\Summary;

/**
 * @databag summary
 * @before checkHostAccess ["meeting", "sessions"]
 * @before getSession
 */
abstract class PageComponent extends \Ajax\PageComponent
{
    use ComponentTrait;
}
