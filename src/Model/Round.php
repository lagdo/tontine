<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;

class Round extends Base
{
    use Traits\HasProperty;

    /**
     * @const
     */
    const STATUS_PENDING = 0;

    /**
     * @const
     */
    const STATUS_OPENED = 1;

    /**
     * @const
     */
    const STATUS_CLOSED = 2;

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
        'status',
        'notes',
    ];

    public function start(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->sessions()->orderBy('start_at')->first(),
        );
    }

    public function startAt(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->start?->start_at ?? null,
        );
    }

    public function end(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->sessions()->orderByDesc('start_at')->first(),
        );
    }

    public function endAt(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->end?->start_at ?? null,
        );
    }

    public function pending(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->status === self::STATUS_PENDING,
        );
    }

    public function opened(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->status === self::STATUS_OPENED,
        );
    }

    public function closed(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->status === self::STATUS_CLOSED,
        );
    }

    public function addDefaultFund(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->properties['savings']['fund']['default'] ?? false,
        );
    }

    public function guild()
    {
        return $this->belongsTo(Guild::class);
    }

    public function funds()
    {
        return $this->hasMany(Fund::class);
    }

    public function pools()
    {
        return $this->hasMany(Pool::class);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function bills()
    {
        return $this->hasMany(RoundBill::class);
    }
}
