<?php

namespace Siak\Tontine\Model;

use Database\Factories\MemberFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Member extends Base
{
    use HasFactory;

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
        'email',
        'phone',
        'address',
        'city',
        'registered_at',
        'birthday',
        'active',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $casts = [
        'registered_at' => 'datetime',
        'birthday' => 'datetime',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory()
    {
        return MemberFactory::new();
    }

    public function guild()
    {
        return $this->belongsTo(Guild::class);
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
     *
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }
}
