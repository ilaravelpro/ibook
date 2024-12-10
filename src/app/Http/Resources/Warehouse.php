<?php


/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 7/21/20, 6:35 PM
 * Copyright (c) 2020. Powered by iamir.net
 */

namespace iLaravel\iBook\iApp\Http\Resources;

use iLaravel\Core\iApp\Http\Resources\Resource;
use iLaravel\iLocation\iApp\Http\Resources\Address;

class Warehouse extends Resource
{
    public function toArray($request)
    {
        $data = parent::toArray($request);
        $address_data = $this->address ? (new Address($this->address))->toArray($request) : [];
        foreach ($address_data as $index => $address_datum) {
            $data['address_' . $index] = $address_datum;
        }
        unset($data['address_id']);
        return $data;
    }
}
