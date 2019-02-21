<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DistrictItem extends Model
{

    protected $fillable = ['distid', 'commid'];

    public function addAll(Array $data)
    {
        $rs = DB::table($this->getTable())->insert($data);
        return $rs;
    }

}
