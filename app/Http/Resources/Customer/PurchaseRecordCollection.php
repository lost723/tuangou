<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PurchaseRecordCollection extends ResourceCollection
{
    protected $records;
    public function __construct($resource, $records)
    {
        parent::__construct($resource);
        $this->records = $records;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'count' => $this->records,
            'data'  => $this->collection,
        ];
    }
}
