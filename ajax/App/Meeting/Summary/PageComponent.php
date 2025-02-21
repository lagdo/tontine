<?php

namespace Ajax\App\Meeting\Summary;

/**
 * @databag meeting
 * @before getSession
 */
abstract class PageComponent extends \Ajax\PageComponent
{
    use ComponentTrait;
}
