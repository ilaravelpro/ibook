<?php
/*
 * Author: Amirhossein Jahani | iAmir.net
 * Email: me@iamir.net
 * Mobile No: +98-9146941147
 * Last modified: 2021/05/20 Thu 03:24 PM IRDT
 * Copyright (c) 2020-2022. Powered by iAmir.net
 */

namespace iLaravel\iBook\iApp;

class Book extends \iLaravel\Core\iApp\Model
{
    public static $s_prefix = 'NMBK';
    public static $s_start = 900;
    public static $s_end = 26999;

    public $files = ['book_index'];

    public static $find_names = ['title', 'slug'];

    public $with_resource = ['prices' => 'Price'];
    public $with_resource_single = ['terms', 'articles', 'accessories', 'awards', 'editions', 'price_olds'];
    public $with_resource_data = ['publisher', 'size', 'cover', 'product'];

    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub
        parent::saving(function (self $event) {
            /*$keys = $event->updateProduct(iLaravel::createFrom(request()));
            foreach ($keys as $key)
                unset($event->$key);*/
        });
    }

    public function getAttribute($key)
    {
        return !($value = parent::getAttribute($key)) && @$this->product ? @$this->product->getAttribute($key) : $value;
    }

    public function updateProduct($request) {
        $request->validationData();
        $requestArray = $request->toArray();
        $product = $this->product?:new Product();
        $exceptAdditional = array_keys(method_exists($product, 'rules') ? $product->rules($request, 'additional', $product) : Product::getRules($request, 'additional', $product));
        $exceptAdditional = array_map(function ($item) {
            return explode('.', $item)[0];
        }, $exceptAdditional);
        $keys = array_keys($this->rules($request, 'product', $this));
        $fields = handel_fields(array_values(array_unique($exceptAdditional)), $keys, $requestArray);
        $dataProduct = [];
        foreach ($fields as $value)
            if (_has_key($requestArray, $value))
                $dataProduct = _set_value($dataProduct, $value, _get_value($requestArray, $value));
        foreach ($dataProduct as $index => $item) {
            if (substr($index, 0, 3) === 'is_' || substr($index, 0, 4) === 'has_') {
                $product->$index = in_array($item, ['true', 'false', '0', '1']) ? intval($item == "true" || $item == "1") : $item;
            }else $product->$index = $item;
        }
        $product->model = "Book";
        $product->model_id = $this->id;
        $product->type = "book";
        $product->save();
        $product->additionalUpdate($request);
        $this->product_id = $product->id;
        $this->save();
        return $product;
    }

    public function getTitleAttribute()
    {
        return $this->product->title;
    }

    public function product()
    {
        return $this->belongsTo(imodal('Product'), 'product_id');
    }

    public function publisher()
    {
        return $this->belongsTo(imodal('Publisher'));
    }

    public function size()
    {
        return $this->belongsTo(imodal('BookSize'));
    }

    public function cover()
    {
        return $this->belongsTo(imodal('BookCover'));
    }

    public function creators()
    {
        return $this->belongsToMany(imodal('BookCreator'), 'books_creators', 'book_id', 'creator_id')->withPivot(['group']);
    }

    public function sounds()
    {
        return $this->belongsToMany(imodal('BookSound'), 'books_sounds', 'book_id', 'sound_id')->withPivot(['link']);
    }
    public function alerts()
    {
        return $this->hasMany(imodal('ProductAlert'), 'product_id', 'product_id');
    }

    public function electronics()
    {
        return $this->belongsToMany(imodal('BookElectronic'), 'books_electronics', 'book_id', 'electronic_id')->withPivot(['link']);
    }

    public function tags()
    {
        return $this->belongsToMany(imodal('Tag'), 'products_tags', 'product_id');
    }
    public function favoritors()
    {
        return $this->belongsToMany(imodal('User'), 'products_favorites', 'product_id', 'user_id');
    }

    public function terms()
    {
        return $this->belongsToMany(imodal('Term'), 'products_terms', 'product_id');
    }

    public function attachments()
    {
        return $this->belongsToMany(imodal('Attachment'), 'products_attachments', 'product_id');
    }

    public function articles()
    {
        return $this->belongsToMany(imodal('Article'), 'products_articles', 'product_id');
    }

    public function accessories()
    {
        return $this->belongsToMany(imodal('Product'), 'products_accessories', 'product_id', 'accessory_id');
    }

    public function awards()
    {
        return $this->hasMany(imodal('ProductAward'), 'product_id', 'product_id');
    }

    public function editions()
    {
        return $this->hasMany(imodal('ProductEdition'), 'product_id', 'product_id');
    }

    public function prices()
    {
        return $this->hasMany(imodal('Price'), 'product_id', 'product_id');
    }

    public function price_olds()
    {
        return $this->hasMany(imodal('PriceOld'), 'product_id', 'product_id');
    }

    public function additionalUpdate($request = null, $additional = null, $parent = null)
    {
        $this->updateProduct($request);
        $this->electronics()->detach();
        foreach ($request->electronics?:[] as $index => $item)
            $this->electronics()->attach($item['electronic_id'], ['link' => $item['link']]);
        $this->sounds()->detach();
        foreach ($request->sounds?:[] as $index => $item)
            $this->sounds()->attach($item['sound_id'], ['link' => $item['link']]);
        $this->creators()->detach();
        foreach (["author", "translator", "collector", "editor", "cover_designer"] as $index) {
            $items = [];
            foreach ($request->{"{$index}s"}?:[] as $item)
                $items[BookCreator::id($item)] = ['group' => $index];
            $this->creators()->attach($items);
        }
        parent::additionalUpdate($request, $additional, $parent);
    }
    public function rules($request, $action, $arg1 = null, $arg2 = null) {
        $arg1 = $arg1 instanceof static ? $arg1 : (is_integer($arg1) ? static::find($arg1) : (is_string($arg1) ? static::findBySerial($arg1) : $arg1));
        $rules = [];
        $additionalRules = [
            'book_index_file' => 'nullable|mimes:jpeg,jpg,png,gif,pdf|max:5120',
        ];
        switch ($action) {
            case 'store':
            case 'update':
                $rules = array_merge($rules, [
                    //'product_id' => "required|exists:products,id",
                    'publisher_id' => "required|exists:publishers,id",
                    'size_id' => "required|exists:book_sizes,id",
                    'cover_id' => "required|exists:book_covers,id",
                    'title_latin' => "nullable|string",
                    'isbn' => "nullable|string",
                    'book_id' => "nullable|string",
                    'width_per_page' => "nullable|numeric",
                    'count_page' => "nullable|numeric",
                ]);
                break;
            case 'product':
                $rules = Product::getRules($request, @$arg1->product ? "update" : "store", @$arg1->product);
                break;
            case 'additional':
                $rules = array_merge($additionalRules, Product::getRules($request, $action, @$arg1->product), Product::getRules($request,  "additional", @$arg1->product));
                break;
        }
        return $rules;
    }


    public static function findByAny($value)
    {
        if (!count(static::$find_names)) return false;
        return static::where('id', static::id($value))->orWhereHas('product',function ($q) use($value) {
            foreach (array_values(static::$find_names) as $index => $name) {
                $q->{$index > 0 ? "orWhere" : "where"}($name, $value);
            }
        })->first();
    }
}