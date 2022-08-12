<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    /**
     * @var Currency
     */
    public static Currency $current;

    /**
     * Format an amount of money
     *
     * @param int $amount
     * @param bool $hideSymbol
     *
     * @return string
     */
    public static function format(int $amount, bool $hideSymbol = false): string
    {
        $options = self::$current->options;
        $amount = number_format($amount, $options->precision,
            $options->separator->decimal, $options->separator->thousand);
        if($hideSymbol)
        {
            return $amount;
        }
        return $options->symbol->swap ? $options->symbol->value . $amount : $amount . ' ' . $options->symbol->value;
    }

    /**
     * Get the currency symbol
     *
     * @return string
     */
    public static function symbol(): string
    {
        return self::$current->options->symbol->value;
    }

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
        'code',
        'options',
    ];

    public function getOptionsAttribute($value)
    {
        return json_decode($value);
    }

    public function setOptionsAttribute($value)
    {
        $this->attributes['options'] = json_encode($value);
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class, 'country_currency');
    }
}
