<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Relations\Pivot;
 
class GuestTontine extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'guest_tontine';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'access',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'access' => 'array',
    ];
}
