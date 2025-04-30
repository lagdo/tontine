<?php

namespace Siak\Tontine\Validation;

use function htmlspecialchars;
use function strtolower;
use function trim;

class SearchSanitizer
{
    /**
     * @param string $search
     *
     * @return string
     */
    public function sanitize(string $search): string
    {
        return htmlspecialchars(strtolower(trim($search)));
    }
}
