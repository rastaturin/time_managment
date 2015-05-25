<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hour extends Model {

    protected $table = 'hours';

    protected $fillable = ['hour', 'user_id'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }


}
