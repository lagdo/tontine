<?php

namespace Siak\Tontine\Model;

class Fund extends Base
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
        'title',
        'notes',
        'active',
    ];

    public function tontine()
    {
        return $this->belongsTo(Tontine::class);
    }
}
