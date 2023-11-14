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
     * @return Attribute
     */
    protected function properties(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->property ? $this->property->content : [],
        );
    }

    /**
     * Save the values of the properties.
     *
     * @param array $content
     *
     * @return void
     */
    public function saveProperties(array $content)
    {
        $this->property ? $this->property->update(['content' => $content]) :
            $this->property()->create(['content' => $content]);
        // Refresh the relation;
        $this->load('property');
    }
}
