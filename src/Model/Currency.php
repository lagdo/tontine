<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Currency extends Model
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
        'options',
    ];

    public function getOptionsAttribute($value)
    {
        return json_decode($value);
    }

    public function setOptionsAttribute($value)
    {
        $this->attributes['options'] = json_encode($value);
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class, 'country_currency');
    }
}
