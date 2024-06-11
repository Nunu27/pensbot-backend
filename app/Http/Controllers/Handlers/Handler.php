<?php

namespace App\Http\Controllers\Handlers;

use App\Models\CrawlData;

class Handler
{
    protected function getData($id)
    {
        $item = CrawlData::find($id);
        return json_decode($item->data);
    }
}
