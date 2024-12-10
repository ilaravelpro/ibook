<?php


/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 9/1/20, 7:44 AM
 * Copyright (c) 2020. Powered by iamir.net
 */

namespace iLaravel\iBook\iApp\Http\Controllers\API\v1;


use iLaravel\Core\iApp\Http\Requests\iLaravel as Request;

class BookCreatorController extends \iLaravel\Core\iApp\Http\Controllers\API\ApiController
{
    public $order_list = ['id', 'image_id', 'title', 'slug', 'summary', 'content', 'order', 'status', 'approved_at', 'updated_at'];

    public function filters($request, $model, $parent = null, $operators = [])
    {
        $filters = [
            [
                'name' => 'all',
                'title' => _t('all'),
                'type' => 'text',
            ],
            [
                'name' => 'title',
                'title' => _t('title'),
                'type' => 'text'
            ],
            [
                'name' => 'slug',
                'title' => _t('slug'),
                'type' => 'text'
            ],
            [
                'name' => 'groups',
                'title' => _t('groups'),
                'type' => 'text'
            ],
            [
                'name' => 'summary',
                'title' => _t('summary'),
                'type' => 'text'
            ],
            [
                'name' => 'content',
                'title' => _t('content'),
                'type' => 'text'
            ],
            [
                'name' => 'order',
                'title' => _t('order'),
                'type' => 'number'
            ],
        ];
        return [$filters, [], $operators];
    }

    public function query_filter_type($model, $filter, $params, $current)
    {
        switch ($params->type){
            case 'groups':
                $groups = is_array($filter->value) ? $filter->value : (is_string($filter->value) ? [$filter->value] : []);
                if (count($groups)) {
                    $model->where(function ($q) use($groups) {
                        foreach ($groups as $index => $group) {
                            $q->{$index == 0 ? 'where' : 'orWhere'}('groups', 'like', "%$group%");
                        }
                    });
                }
                $current['groups'] = $filter->value;
                break;
        }
        return $current;
    }
}
