<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

use function trans;

class Category extends Base
{
    /**
     * @const
     */
    const TYPE_OUTFLOW = 'outflow';

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
        'item_type',
        'active',
    ];

    /**
     * @return Attribute
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn($value) => !$this->guild ? trans('meeting.category.types.' . $value) : $value,
        );
    }

    /**
     * @return Attribute
     */
    protected function isOther(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->attributes['name'] === 'other',
        );
    }

    public function guild()
    {
        return $this->belongsTo(Guild::class);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeGlobal(Builder $query): Builder
    {
        return $query->whereNull('guild_id');
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeOutflow(Builder $query): Builder
    {
        return $query->where('item_type', self::TYPE_OUTFLOW);
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
