<?php

namespace Siak\Tontine\Model;

class User extends \App\Models\User
{
    public function tontines()
    {
        return $this->hasMany(Tontine::class)->orderBy('tontines.id', 'desc');
    }
}
