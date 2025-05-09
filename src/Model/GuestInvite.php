<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

use function now;
use function trans;

class GuestInvite extends Base
{
    use Traits\DateFormatter;

    /**
     * @const int
     */
    const STATUS_PENDING = 0;

    /**
     * @const int
     */
    const STATUS_REFUSED = 1;

    /**
     * @const int
     */
    const STATUS_ACCEPTED = 2;

    /**
     * @const int
     */
    const STATUS_CANCELLED = 3;

    /**
     * @var array<string>
     */
    static protected $statuses = ['pending', 'refused', 'accepted', 'cancelled'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status',
        'active',
        'expires_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    public function host()
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function guest()
    {
        return $this->belongsTo(User::class, 'guest_id');
    }

    public function guilds()
    {
        return $this->belongsToMany(Guild::class,
            'guest_options', 'invite_id', 'guild_id')
            ->as('options')
            ->withPivot('access')
            ->using(GuestOptions::class);
    }

    /**
     * @return Attribute
     */
    protected function statusLabel(): Attribute
    {
        $status = $this->status === self::STATUS_PENDING && $this->expires_at < now() ?
            'expired' : (self::$statuses[$this->status] ?? 'unknown');
        return Attribute::make(
            get: fn() => trans("tontine.invite.status.$status"),
        );
    }

    /**
     * @return Attribute
     */
    protected function activeLabel(): Attribute
    {
        return Attribute::make(
            get: function() {
                if($this->status === self::STATUS_PENDING) {
                    $label = $this->expires_at < now() ? 'expired' : 'expires';
                    return trans("tontine.invite.active.$label", [
                        'date' => $this->date('expires_at', 'format_medium'),
                    ]);
                }
                if($this->status === self::STATUS_ACCEPTED) {
                    $label = $this->active ? 'active' : 'inactive';
                    return trans("tontine.invite.active.$label", [
                        'date' => $this->date('updated_at', 'format_medium'),
                    ]);
                }
                return null;
            },
        );
    }

    /**
     * @return Attribute
     */
    protected function isExpired(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->status === self::STATUS_PENDING && $this->expires_at < now(),
        );
    }

    /**
     * @return Attribute
     */
    protected function isPending(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->status === self::STATUS_PENDING,
        );
    }

    /**
     * @return Attribute
     */
    protected function isRefused(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->status === self::STATUS_REFUSED,
        );
    }

    /**
     * @return Attribute
     */
    protected function isAccepted(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->status === self::STATUS_ACCEPTED,
        );
    }

    /**
     * @return Attribute
     */
    protected function isCancelled(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->status === self::STATUS_CANCELLED,
        );
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('expires_at', '<', now());
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('expires_at', '>=', now())
            ->where('status', self::STATUS_PENDING);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeRefused(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REFUSED);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeAccepted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACCEPTED);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    /**
     * @param Builder $query
     * @param User $user
     *
     * @return Builder
     */
    public function scopeOfUser(Builder $query, User $user): Builder
    {
        return $query->where(fn(Builder $_query) =>
            $_query->orWhere('host_id', $user->id)
                ->orWhere('guest_id', $user->id));
    }
}
