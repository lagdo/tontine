<?php

namespace Siak\Tontine\Model;

use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
{
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }
}
