<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Employee extends Authenticatable
{
    use HasFactory;
    protected $fillable = [
        'email',
        'password',
        'f_name',
        'l_name',
        'region',
        'city',
        'town',
        'section_id'
    ];

     /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        //'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [

    ];

    public function section()
    {
        return $this->belongsTo('App\Models\Section', 'section_id', 'id');
    }
}
