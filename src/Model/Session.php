<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'abbrev',
        'agenda',
        'report',
        'status',
        'notes',
        'venue',
        'start_at',
        'end_at',
        'host_id',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'start_at',
        'end_at',
    ];

    public function getAbbrevAttribute($value)
    {
        return $value ?? $this->start_at->format('M y');
    }

    public function getDateAttribute()
    {
        return $this->start_at->format('l jS F Y');
    }

    public function getTimesAttribute()
    {
        return $this->start_at->format('H:i') . ' - ' . $this->end_at->format('H:i');
    }

    public function getPendingAttribute()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function getOpenedAttribute()
    {
        return $this->status === self::STATUS_OPENED;
    }

    public function getClosedAttribute()
    {
        return $this->status === self::STATUS_CLOSED;
    }

    public function round()
    {
        return $this->belongsTo(Round::class);
    }

    public function host()
    {
        return $this->belongsTo(Member::class);
    }

    public function payables()
    {
        return $this->hasMany(Payable::class)->orderBy('payables.id', 'asc');
    }

    public function receivables()
    {
        return $this->hasMany(Receivable::class)->orderBy('receivables.id', 'asc');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function biddings()
    {
        return $this->hasMany(Bidding::class);
    }

    public function enabled(Fund $fund)
    {
        return in_array($this->id, $fund->session_ids);
    }

    public function disabled(Fund $fund)
    {
        return !in_array($this->id, $fund->session_ids);
    }
}
