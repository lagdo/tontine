<?php

namespace Siak\Tontine\Model;

use Database\Factories\MemberFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class MemberDef extends Base
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
        'active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'registered_at' => 'datetime',
        ];
    }

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

    public function members()
    {
        return $this->hasMany(Member::class, 'def_id');
    }

    /**
     * @param  Builder  $query
     * @param  bool     $active
     *
     * @return Builder
     */
    public function scopeActive(Builder $query, bool $active = true): Builder
    {
        return $query->where('active', $active);
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
            ->when($search !== '', fn($q) => $q
                ->where(DB::raw('lower(name)'), 'like', "%{$search}%"));
    }
}
