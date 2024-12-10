<?php


/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 7/21/20, 6:35 PM
 * Copyright (c) 2020. Powered by iamir.net
 */

namespace iLaravel\iBook\iApp\Http\Resources;

use iLaravel\Core\iApp\Http\Resources\File;
use iLaravel\Core\iApp\Http\Resources\ResourceData;

class BookCreatorData extends ResourceData
{
    public function toArray($request)
    {
        $data = parent::toArray($request);
        if ($file = $this->image ?: $this->resource->getFile('image')) $data['image'] = $file['original']->url;
        return $data;
    }
}
