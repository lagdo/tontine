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
    const TYPE_DISBURSEMENT = 'disbursement';

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
     * Get the name.
     *
     * @return Attribute
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn($value) => !$this->tontine ? trans('meeting.category.types.' . $value) : $value,
        );
    }

    public function tontine()
    {
        return $this->belongsTo(Tontine::class);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeDisbursement(Builder $query): Builder
    {
        return $query->where('item_type', self::TYPE_DISBURSEMENT);
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
