<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Relations\Pivot;
 
class GuestGuild extends Pivot
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'access' => 'array',
        ];
    }
}
