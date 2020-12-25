<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Reguest extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'subject',
        'details',
        'user_id',
        'section_id'
    ];


    public function Statuses()
    {
        return $this->hasMany('App\Models\Statuses', 'request_id', 'id');
    }

    public function Ratings()
    {
        return $this->hasOne('App\Models\Ratings', 'request_id', 'id');
    }

    public function Suggestions()
    {
        return $this->hasOne('App\Models\Suggestions', 'request_id', 'id');
    }


}
