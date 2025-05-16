<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\DB;

class Member extends Base
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
        'round_id',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
        'def',
    ];

    public function name(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->def->name,
        );
    }

    public function def()
    {
        return $this->belongsTo(MemberDef::class, 'def_id');
    }

    public function round()
    {
        return $this->belongsTo(Round::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function libre_bills()
    {
        return $this->hasMany(LibreBill::class);
    }

    public function session_bills()
    {
        return $this->hasMany(SessionBill::class);
    }

    public function round_bills()
    {
        return $this->hasMany(RoundBill::class);
    }

    public function oneoff_bills()
    {
        return $this->hasMany(OneoffBill::class);
    }

    public function savings()
    {
        return $this->hasMany(Saving::class);
    }

    public function absences()
    {
        return $this->belongsToMany(Session::class, 'absences');
    }

    /**
     * @param  Builder  $query
     * @param  string $search
     *
     * @return Builder
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query
            ->when($search !== '', fn($qm) => $qm
                ->whereHas('def', fn($qd) => $qd
                    ->where(DB::raw('lower(name)'), 'like', "%{$search}%")));
    }
}
