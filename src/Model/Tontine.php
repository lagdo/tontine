<?php

namespace Siak\Tontine\Model;

use Database\Factories\TontineFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tontine extends Base
{
    use HasFactory;
    use Traits\HasProperty;

    /**
     * True if the tontine is accessed as guest.
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
        'type',
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
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'type' => 'x',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
        'default_fund',
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
        return TontineFactory::new();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function members()
    {
        return $this->hasMany(Member::class)->orderBy('name', 'asc');
    }

    public function rounds()
    {
        return $this->hasMany(Round::class)->orderBy('rounds.id', 'desc');
    }

    public function sessions()
    {
        return $this->hasManyThrough(Session::class, Round::class)
            ->select('sessions.*');
    }

    public function funds()
    {
        return $this->hasMany(Fund::class);
    }

    /**
     * Get the default savings fund.
     */
    public function default_fund()
    {
        return $this->hasOne(Fund::class)
            ->withoutGlobalScope('user')
            ->ofMany(['id' => 'max'], fn(Builder $query) => $query->where('title', ''));
    }

    public function charges()
    {
        return $this->hasMany(Charge::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function invites()
    {
        return $this->belongsToMany(GuestInvite::class,
            'guest_tontine', 'tontine_id', 'invite_id')
            ->as('permission')
            ->withPivot('access')
            ->using(GuestTontine::class);
    }
}
