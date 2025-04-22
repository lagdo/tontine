<?php

namespace Siak\Tontine\Model\Traits;

use function trans;

trait DateFormatter
{
    /**
     * @param string $field
     * @param string $format
     *
     * @return string
     */
    public function date(string $field, string $format = 'format'): string
    {
        return $this->$field->translatedFormat(trans("tontine.date.$format"));
    }
}
