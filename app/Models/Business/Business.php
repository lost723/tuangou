<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    const  Normal = 9;
    const  Frozen = 1;
    const  Created = 0;
    protected $fillable = ['title', 'phone', 'manager', 'address'];
}
