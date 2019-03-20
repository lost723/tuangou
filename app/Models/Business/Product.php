<?php

namespace App\Models\Business;


use App\Models\BaseModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Product extends BaseModel
{
    const On = 9;  # 正常，在货架
    const Off = 1; # 下架，活动不可见
    const Del = 0;

    # 用下面这些
    const Active = 9;   # 正常，在货架
    const Disable = 1;  # 下架，活动不可见
    const Deleted = 0;      # 删除，商家不可见

    protected $fillable = [
        'title', 'intro', 'thumb', 'price', 'content', 'norm', 'mtpd', 'rate',
        'quotation', 'picture', 'orgid', 'optid', 'distid', 'catid'
    ];


    /**
     * 获取某商户的商品列表
     * @param $request
     * @return
     */
    static function getBusinessOwnList($request)
    {
        $orgid = Auth::user()->orgid;
        $ids = $request->get('ids');
        $date = $request->get('date');
        $status = $request->get('status');
        $filter = $request->get('filter');

        $result = DB::table(with(new Product())->getTable().' as pd')
                ->where('pd.orgid', $orgid)
                ->where('pd.status', '!=', self::Del)
                ->when($date, function ($query) use ($date) {
                    $query->where('pd.created_at','>=', ($date[0]))
                        ->where('pd.created_at','<', ($date[1]));
                })
                ->when($ids, function ($query) use ($ids) {
                    $query->wherein('pd.id', $ids);
                })
                ->when($status, function ($query) use ($status) {
                    $query->where('pd.status', $status);
                })
                ->when($filter, function ($query) use ($filter){
                    $query->where('pd.title', 'like', "%$filter%");
                })
                ->leftjoin(with(new District())->getTable().' as dt', 'pd.distid', '=', 'dt.id')
                ->select('pd.id', 'pd.title', 'intro', 'thumb', 'price', 'rate', 'quotation', 'issue',
                         'norm', 'pd.status', 'dt.title as district')
            ->paginate(self::NPP);

        return self::paginationFormater($result);
    }

}
