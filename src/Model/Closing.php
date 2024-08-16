<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

use function json_encode;

class Closing extends Base
{
    /**
     * @var string
     */
    const TYPE_SAVINGS = 's';

    /**
     * @var string
     */
    const TYPE_INTEREST = 'i';

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
        'type',
        'options',
        'session_id',
        'fund_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'options' => 'array',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'options' => '{}',
    ];

    /**
     * Get the profit amount.
     *
     * @return Attribute
     */
    protected function profit(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->options['profit']['amount'] ?? 0,
            set: function(int $amount) {
                $options = $this->options;
                $options['profit']['amount'] = $amount;
                // Return the fields to be set on the model.
                return ['options' => json_encode($options)];
            },
        );
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function fund()
    {
        return $this->belongsTo(Fund::class)->withoutGlobalScope('user');
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeSavings(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_SAVINGS);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeInterest(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_INTEREST);
    }
}
