<?php

namespace Siak\Tontine\Model;

use Database\Factories\GuildFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Guild extends Base
{
    use HasFactory;
    use Traits\HasProperty;

    /**
     * True if the guild is accessed as guest.
     *
     * @var bool
     */
    public $isGuest = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'shortname',
        'biography',
        'email',
        'phone',
        'address',
        'city',
        'website',
        'country_code',
        'currency_code',
    ];

    /**
     * @return Attribute
     */
    protected function locale(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->user->locale,
        );
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory()
    {
        return GuildFactory::new();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function members()
    {
        return $this->hasMany(MemberDef::class);
    }

    public function rounds()
    {
        return $this->hasMany(Round::class);
    }

    public function sessions()
    {
        return $this->hasManyThrough(Session::class, Round::class)
            ->select('sessions.*');
    }

    public function pools()
    {
        return $this->hasMany(PoolDef::class);
    }

    public function funds()
    {
        return $this->hasMany(FundDef::class);
    }

    /**
     * Get the default savings fund.
     */
    public function default_fund()
    {
        return $this->hasOne(FundDef::class)
            ->ofMany(['id' => 'max'], fn(Builder $query) => $query->auto());
    }

    public function charges()
    {
        return $this->hasMany(ChargeDef::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function invites()
    {
        return $this->belongsToMany(GuestInvite::class,
            'guest_options', 'guild_id', 'invite_id')
            ->as('options')
            ->withPivot('access')
            ->using(GuestOptions::class);
    }
}
