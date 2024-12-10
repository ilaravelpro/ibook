<?php


/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 7/21/20, 6:35 PM
 * Copyright (c) 2020. Powered by iamir.net
 */

namespace iLaravel\iBook\iApp\Http\Resources;

use iLaravel\Core\iApp\Http\Resources\ResourceData;

class BookData extends ResourceData
{
    public function toArray($request)
    {
        $data = parent::toArray($request);
        $data['slug'] = $this->slug;
        return $data;
    }
}
