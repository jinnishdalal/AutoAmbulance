<?php

namespace Chatify\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    public function from_data()
    {
        return $this->hasOne('App\User', 'id', 'from_id');
    }
}
