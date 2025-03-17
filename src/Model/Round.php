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

    public function startAt(): Attribute
    {
        return Attribute::make(
            get: function() {
                $startSession = $this->sessions()->orderBy('start_at')->first();
                return !$startSession ? null : $startSession->start_at;
            },
        );
    }

    public function endAt(): Attribute
    {
        return Attribute::make(
            get: function() {
                $endSession = $this->sessions()->orderByDesc('start_at')->first();
                return !$endSession ? null : $endSession->start_at;
            },
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

    public function tontine()
    {
        return $this->belongsTo(Tontine::class);
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
