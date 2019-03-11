<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    const NPP = 15; # 每页条数

    static public function paginationFormater($dat)
    {
        $result['data'] = $dat->items();
        $result['pagination']['total'] = $dat->total();
        $result['pagination']['pageSize'] = $dat->perPage();
        $result['pagination']['current'] = $dat->currentPage();
        $result['pagination']['defaultPageSize'] = self::NPP;
        return $result;
    }
}
