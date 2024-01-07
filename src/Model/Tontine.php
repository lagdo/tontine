<?php

namespace Siak\Tontine\Model;

use Database\Factories\TontineFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tontine extends Base
{
    use HasFactory;
    use Traits\HasProperty;

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

    public function charges()
    {
        return $this->hasMany(Charge::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }
}
