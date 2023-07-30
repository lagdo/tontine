<?php

namespace Siak\Tontine\Model;

class Property extends Base
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
        'content',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'content' => 'array',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'content' => '{}',
    ];

    /**
     * Get the parent owner model.
     */
    public function owner()
    {
        return $this->morphTo();
    }
}
