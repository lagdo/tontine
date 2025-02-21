<?php

namespace Ajax\App\Meeting;

/**
 * @databag meeting
 * @before getSession
 */
abstract class Component extends \Ajax\Component
{
    use ComponentTrait;
}
