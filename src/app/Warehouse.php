<?php
/*
 * Author: Amirhossein Jahani | iAmir.net
 * Email: me@iamir.net
 * Mobile No: +98-9146941147
 * Last modified: 2021/05/20 Thu 03:24 PM IRDT
 * Copyright (c) 2020-2022. Powered by iAmir.net
 */

namespace iLaravel\iBook\iApp;
class Warehouse extends \iLaravel\Core\iApp\Model
{
    public static $s_prefix = 'NMBC';
    public static $s_start = 900;
    public static $s_end = 26999;
    public static $find_names = ['title', 'slug'];
    public $files = ['image'];

    protected $casts = [
        'groups' => 'array'
    ];

    public function creator()
    {
        return $this->belongsTo(imodal('User'));
    }

    public function address()
    {
        return $this->belongsTo(imodal('Address'));
    }

    public function additionalUpdate($request = null, $additional = null, $parent = null)
    {
        $address = [];
        if ($request->get('address_cities')) {
            $cities = $request->get('address_cities');
            $address['city_id'] = \iLaravel\iLocation\iApp\City::id(end($cities));
        }
        $address['title'] = $this->title;
        $address['text'] = $request->address_text;
        $address['postcode'] = $request->address_postcode;
        $address['longitude'] = $request->address_longitude;
        $address['latitude'] = $request->address_latitude;
        $model = $this->address?: $this->address()->create();
        $model->update($address);
        $this->address_id = $model->id;
        parent::additionalUpdate($request, $additional, $parent);
    }
    public function rules($request, $action, $arg1 = null, $arg2 = null) {
        $arg1 = $arg1 instanceof static ? $arg1 : (is_integer($arg1) ? static::find($arg1) : (is_string($arg1) ? static::findBySerial($arg1) : $arg1));
        $rules = [];
        $additionalRules = [
            'image_file' => 'nullable|mimes:jpeg,jpg,png,gif|max:5120',
            'address_cities' => "nullable|exists_serial:City",
            'address_text' => "required|string",
            'address_postcode' => "required|string",
            'address_longitude' => "required|longitude",
            'address_latitude' => "required|latitude",
        ];
        switch ($action) {
            case 'store':
            case 'update':
                $rules = array_merge($rules, [
                    'title' => "required|string",
                    'slug' => ['nullable','string'],
                    'template' => 'nullable|string',
                    'summary' => "nullable|string",
                    'content' => "nullable|string",
                    'is_default' => "nullable|boolean",
                    'status' => 'nullable|in:' . join( ',', iconfig('status.warehouses', iconfig('status.global'))),
                ]);
                break;
            case 'additional':
                $rules = $additionalRules;
                break;
        }
        return $rules;
    }
}
