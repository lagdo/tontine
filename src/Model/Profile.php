<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
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
        'properties',
        'address',
        'city',
        'country_code',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'properties' => '{}',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
