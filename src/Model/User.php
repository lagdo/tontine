<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends \App\Models\User
{
    use Traits\HasProperty;

    /**
     * Get the values of the properties.
     *
     * @return Attribute
     */
    protected function city(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->profile ? $this->profile->city : '',
        );
    }

    /**
     * Get the values of the properties.
     *
     * @return Attribute
     */
    protected function countryCode(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->profile ? $this->profile->country_code : '',
        );
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function tontines()
    {
        return $this->hasMany(Tontine::class)->orderBy('tontines.id', 'desc');
    }
}
