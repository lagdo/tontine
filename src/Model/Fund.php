<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Builder;

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

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Scope for user defined funds. Always applied by default.
        static::addGlobalScope('user', function (Builder $builder) {
            $builder->where('title', '<>', '');
        });
    }

    public function tontine()
    {
        return $this->belongsTo(Tontine::class);
    }

    public function savings()
    {
        return $this->hasMany(Saving::class);
    }

    public function closings()
    {
        return $this->hasMany(Closing::class);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }
}
