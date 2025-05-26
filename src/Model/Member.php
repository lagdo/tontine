<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Builder;
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
        'def_id',
        'round_id',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Also select fields from the member_defs table.
        static::addGlobalScope('def', fn(Builder $query) => $query
            ->addSelect(['members.*', 'd.name'])
            ->join(DB::raw('member_defs as d'), 'd.id', '=', 'members.def_id'));
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

    public function onetime_bills()
    {
        return $this->hasMany(OnetimeBill::class);
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
