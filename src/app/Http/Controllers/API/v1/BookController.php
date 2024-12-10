<?php


/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 9/1/20, 7:44 AM
 * Copyright (c) 2020. Powered by iamir.net
 */

namespace iLaravel\iBook\iApp\Http\Controllers\API\v1;


use iLaravel\Core\iApp\Exceptions\iException;
use iLaravel\Core\iApp\Http\Requests\iLaravel as Request;
use Morilog\Jalali\Jalalian;

class BookController extends \iLaravel\Core\iApp\Http\Controllers\API\ApiController
{
    public $order_list = ['id', 'title', 'slug', 'summary', 'content', 'order', 'status', 'approved_at'];

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
                'name' => 'terms',
                'title' => _t('terms'),
                'rule' => 'required|exists_serial:Term',
                'type' => 'text'
            ],
            [
                'name' => 'slug',
                'title' => _t('slug'),
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
            [
                'name' => 'publisher_id',
                'title' => _t('publisher'),
                'type' => 'text'
            ],
            [
                'name' => 'collection_id',
                'title' => _t('collection'),
                'type' => 'text'
            ],
            [
                'name' => 'creator_id',
                'title' => _t('creator'),
                'type' => 'text'
            ],
            [
                'name' => 'views',
                'title' => _t('views'),
                'type' => 'hidden'
            ],
        ];
        $model->with(['product', 'prices'/*, 'creators', 'sounds', 'electronics'*/]);
        if (auth()->check()) {
            $model->with(['favoritors']);
        }
        if (in_array($request->related_resource, ['pages', 'product', 'book']) &&
            $request->related_id && ($book = imodal('Book')::findByAny($request->related_id))) {
            $model->where('id', '!=', $book->id);
            switch ($request->type_data) {
                case 'collection_products':
                    $model->whereHas('product', function ($q) use ($book) {
                        $q->where('collection_id', $book->product->collection_id);
                    });
                    break;
                default:
                    $model
                        ->where(function ($query) use ($book) {
                            $query->whereExists(function ($query) use ($book) {
                                $query->select(\DB::raw(1))
                                    ->from('products_terms')
                                    ->whereColumn('products_terms.product_id', 'books.product_id');
                                $query->whereIn('products_terms.term_id', $book->terms->pluck('id')->toArray());
                            });
                            $query->orWhereExists(function ($query) use ($book) {
                                $query->select(\DB::raw(1))
                                    ->from('books_creators')
                                    ->whereColumn('books_creators.book_id', 'books.id');
                                $query->whereIn('books_creators.creator_id', $book->creators->pluck('id')->toArray());
                            });
                        });
                    break;
            }
        }
        if (is_array($request->years) && count($request->years)) {
            $start = (new Jalalian($request->years[0], 01, 01))->toCarbon()->format('Y-m-d H:i:s');
            $end = (new Jalalian($request->years[1], 01, 01))->toCarbon()->format('Y-m-d H:i:s');
            $model->whereHas('product', function ($q) use ($start, $end) {
                $q->whereBetween('first_published_at', [$start, $end]);
            });
        }
        if (is_string($request->creator) && strlen($request->creator) > 2) {
            $model->whereHas('creators', function ($q) use ($request) {
                $q->whereRaw("CONCAT(book_creators.name, ' ', book_creators.family) like '%$request->creator%'");
            });
        }
        if ($request->is_my_alerts && $request->is_my_alerts == 1) {
            $model->whereHas('alerts', function ($q) use ($request) {
                $q->where('creator_id', auth()->id());
            });
        }
        if ($request->is_my_favorites && $request->is_my_favorites == 1) {
            $model->whereHas('favoritors', function ($q) use ($request) {
                $q->where('users.id', auth()->id());
            });
        }
        return [$filters, [], $operators];
    }

    public function query_filter_type($model, $filter, $params, $current)
    {
        switch ($params->type) {
            case 'terms':
                $termModel = imodal('Term');
                if ($params->value)
                $model->whereHas('terms', function ($query) use ($params, $termModel) {
                    $query->whereIn('terms.id', array_map(function ($serial) use ($termModel) {
                        return $termModel::id($serial) ? : (@$termModel::findByAny($serial)->id);
                    }, $params->value));
                    return $query;
                });
                $current['terms'] = $filter->value;
                break;
            case 'publisher_id':
                $termModel = imodal('Publisher');
                if ($params->value)
                $model->whereHas('publisher', function ($query) use ($params, $termModel) {
                    $query->whereIn('publishers.id', array_map(function ($serial) use ($termModel) {
                        return $termModel::id($serial) ? : (@$termModel::findByAny($serial)->id);
                    }, $params->value));
                    return $query;
                });
                $current['publisher_id'] = $filter->value;
                break;
            case 'collection_id':
                $termModel = imodal('ProductCollection');
                if ($params->value)
                    $model->whereHas('product', function ($query) use ($params, $termModel) {
                        $query->whereIn('products.collection_id', array_map(function ($serial) use ($termModel) {
                            return $termModel::id($serial) ? : (@$termModel::findByAny($serial)->id);
                        }, $params->value));
                        return $query;
                    });
                $current['collection_id'] = $filter->value;
                break;
            case 'creator_id':
                $termModel = imodal('BookCreator');
                if ($params->value)
                    $model->whereHas('creators', function ($query) use ($params, $termModel) {
                        $query->whereIn('book_creators.id', array_map(function ($serial) use ($termModel) {
                            return $termModel::id($serial) ? : (@$termModel::findByAny($serial)->id);
                        }, $params->value));
                        return $query;
                    });
                $current['creator_id'] = $filter->value;
                break;
            case 'views':
                $items = $filter->value;
                if (is_array($items) && count($items)) {
                    if (in_array('is_stock', $items)) {
                        $model->whereHas('prices', function ($query) {
                            $query->where('prices.stock', '>', 0);
                            return $query;
                        });
                    }
                    if (in_array('is_publishing', $items)) {
                        $model->whereHas('product', function ($query) {
                            $query->where('products.is_publishing', '=', 1);
                            return $query;
                        });
                    }
                    if (in_array('is_sound', $items)) {
                        $model->withCount('sounds')->having('sounds_count', '>', 0);
                    }
                    if (in_array('is_electronic', $items)) {
                        $model->withCount('electronics')->having('electronics_count', '>', 0);
                    }
                }
                $current['views'] = $filter->value;
                break;
        }
        return $current;
    }


    public function favorite(Request $request, $record)
    {
        if ($record = $this->model::findByAny($record)) {
            if ($favoritor = $record->favoritors()->where('users.id', auth()->id())->first()) {
                $this->statusMessage = 'از لیست علاقه‌مندی های شما حذف شد.';
                $record->favoritors()->detach(auth()->id());
            }else {
                $this->statusMessage = 'به لیست علاقه‌مندی های شما افزوده شد.';
                $record->favoritors()->attach(auth()->id());
            }
            return ['data' => $this->_show($request, $record)];
        }else
            throw new iException('اطلاعاتی یافت نشد.');
    }
}
