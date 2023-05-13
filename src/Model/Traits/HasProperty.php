<?php

namespace Siak\Tontine\Model\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Siak\Tontine\Model\Property;

trait HasProperty
{
    /**
     * Get the property model.
     */
    public function property()
    {
        return $this->morphOne(Property::class, 'owner');
    }

    /**
     * Get the values of the properties.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function properties(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->property ? $this->property->content : [],
        );
    }
}
