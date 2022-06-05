<?php

namespace Siak\Tontine\Model;

use Database\Factories\MemberFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
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
        'gender',
        'email',
        'phone',
        'address',
        'city',
        'registered_at',
        'birthday',
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
        'registered_at',
        'birthday',
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

    public function tontine()
    {
        return $this->belongsTo(Tontine::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function settlements()
    {
        return $this->hasMany(Settlement::class);
    }
}
