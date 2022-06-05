<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'phone',
        'languages',
        'operators',
    ];

    public function getLanguagesAttribute($value)
    {
        return json_decode($value);
    }

    public function setLanguagesAttribute($value)
    {
        $this->attributes['languages'] = json_encode($value);
    }

    public function getOperatorsAttribute($value)
    {
        return json_decode($value);
    }

    public function setOperatorsAttribute($value)
    {
        $this->attributes['operators'] = json_encode($value);
    }

    public function tontines()
    {
        return $this->hasMany(Tontine::class);
    }

    public function schools()
    {
        return $this->hasMany(School::class);
    }

    public function currencies()
    {
        return $this->belongsToMany(Currency::class, 'country_currency');
    }
}
