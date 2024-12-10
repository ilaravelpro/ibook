<?php
/*
 * Author: Amirhossein Jahani | iAmir.net
 * Email: me@iamir.net
 * Mobile No: +98-9146941147
 * Last modified: 2021/05/20 Thu 03:24 PM IRDT
 * Copyright (c) 2020-2022. Powered by iAmir.net
 */

namespace iLaravel\iBook\iApp;

class BookCreator extends \iLaravel\Core\iApp\Model
{
    public static $s_prefix = 'NMBC';
    public static $s_start = 900;
    public static $s_end = 26999;
    public static $find_names = ['title', 'slug'];
    public  $appends = ['fullname'];

    public $files = ['image'];

    protected $casts = [
        'groups' => 'array'
    ];

    public function creator()
    {
        return $this->belongsTo(imodal('User'), 'creator_id');
    }


    public function books()
    {
        return $this->belongsToMany(imodal('Book'), 'books_creators');
    }

    public function getTitleAttribute()
    {
        return $this->fullname;
    }

    public function getFullnameAttribute()
    {
        return implode(' ',array_filter([$this->name, $this->family], 'strlen')) ? : null;
    }

    public function rules($request, $action, $arg1 = null, $arg2 = null) {
        $arg1 = $arg1 instanceof static ? $arg1 : (is_integer($arg1) ? static::find($arg1) : (is_string($arg1) ? static::findBySerial($arg1) : $arg1));
        $rules = [];
        $additionalRules = [
            'image_file' => 'nullable|mimes:jpeg,jpg,png,gif|max:5120',
            //'tags.*' => "nullable",
        ];
        switch ($action) {
            case 'store':
            case 'update':
                $rules = array_merge($rules, [
                    'title' => "required|string",
                    'slug' => ['nullable','string'],
                    'name' => "nullable|string",
                    'family' => "nullable|string",
                    'groups.*' => "nullable|string",
                    'gender' => "nullable|string",
                    'nationality' => "nullable|string",
                    'template' => "nullable|string",
                    'summary' => "nullable|string",
                    'content' => "nullable|string",
                    'status' => 'nullable|in:' . join( ',', iconfig('status.book_creators', iconfig('status.global'))),
                ]);
                break;
            case 'additional':
                $rules = $additionalRules;
                break;
        }
        return $rules;
    }
}
