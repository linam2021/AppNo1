<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function Requests()
    {
        return $this->hasMany('App\Models\Requests', 'section_id', 'id');
    }
    
    public function Employees()
    {
        return $this->hasMany('App\Models\Employees', 'section_id', 'id');
    }
}
