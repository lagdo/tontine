<?php

namespace App\Ajax;

use Siak\Tontine\Model\Tontine;
use Siak\Tontine\Model\User;
use Jaxon\App\CallableClass as JaxonCallableClass;
use Jaxon\App\Dialog\MessageInterface;
use Jaxon\App\Dialog\ModalInterface;

use function floor;

class CallableClass extends JaxonCallableClass
{
    /**
     * @var User|null
     */
    public ?User $user;

    /**
     * @var Tontine|null
     */
    public ?Tontine $tontine;

    /**
     * @var ModalInterface
     */
    public $dialog;

    /**
     * @var MessageInterface
     */
    public $notify;

    /**
     * Get the page number to show
     *
     * @param int $pageNumber
     * @param int $itemCount
     * @param string $bagName
     * @param string $attrName
     *
     * @return array
     */
    protected function pageNumber(int $pageNumber, int $itemCount, string $bagName, string $attrName = 'page'): array
    {
        $perPage = 10;
        $pageCount = (int)floor($itemCount / $perPage) + ($itemCount % $perPage > 0 ? 1 : 0);
        if($pageNumber > $pageCount)
        {
            $pageNumber = $pageCount;
        }
        if($pageNumber < 1)
        {
            $pageNumber = $this->bag($bagName)->get($attrName, 1);
        }
        $this->bag($bagName)->set($attrName, $pageNumber);

        return [$pageNumber, 10];
    }
}
 