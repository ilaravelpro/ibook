<?php
/*
 * Author: Amirhossein Jahani | iAmir.net
 * Email: me@iamir.net
 * Mobile No: +98-9146941147
 * Last modified: 2021/05/20 Thu 03:24 PM IRDT
 * Copyright (c) 2020-2022. Powered by iAmir.net
 */

namespace iLaravel\iBook\iApp;

use App\Models\BaseModel;

class Publisher extends BaseModel
{
    public static $s_prefix = 'NMP';
    public static $s_start = 900;
    public static $s_end = 26999;
    public $set_slug = 'title';
    public static $find_names = ['title', 'slug'];

    public $files = ['logo'];

    public $with_resource = [];

    public function creator()
    {
        return $this->belongsTo(imodal('User'));
    }

    public function tags()
    {
        return $this->belongsToMany(imodal('Tag'), 'publishers_tags');
    }

    public function rules($request, $action, $arg1 = null, $arg2 = null) {
        $arg1 = $arg1 instanceof static ? $arg1 : (is_integer($arg1) ? static::find($arg1) : (is_string($arg1) ? static::findBySerial($arg1) : $arg1));
        $rules = [];
        $additionalRules = [
            'logo_file' => 'nullable|mimes:jpeg,jpg,png,gif|max:5120',
            'tags.*' => "nullable",
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
                    'website' => "nullable|string",
                    'status' => 'nullable|in:' . join( ',', iconfig('status.publishers', iconfig('status.global'))),
                ]);
                break;
            case 'additional':
                $rules = $additionalRules;
                break;
        }
        return $rules;
    }
}
