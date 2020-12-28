<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'subject',
        'details',
        'user_id',
        'section_id'
    ];


    public function status()
    {
        return $this->hasMany('App\Models\Status', 'request_id', 'id');
    }

    public function rating()
    {
        return $this->hasOne('App\Models\Rating', 'request_id', 'id');
    }

    public function suggestion()
    {
        return $this->hasOne('App\Models\Suggestion', 'request_id', 'id');
    }


}
