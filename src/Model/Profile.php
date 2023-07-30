<?php

namespace Siak\Tontine\Model;

class Profile extends Base
{
    use Traits\HasProperty;

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
        'address',
        'city',
        'country_code',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
