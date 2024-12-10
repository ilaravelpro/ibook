<?php


/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 7/21/20, 6:35 PM
 * Copyright (c) 2020. Powered by iamir.net
 */

namespace iLaravel\iBook\iApp\Http\Resources;

use iLaravel\Core\iApp\Http\Resources\Resource;
use iLaravel\Core\iApp\Http\Resources\ResourceData;

class Book extends Resource
{
    public function toArray($request)
    {
        $data = parent::toArray($request);
        $product_resource = iresource('Product');
        $product_data = $this->product ? (new $product_resource($this->product))->toArray($request) : [];
        unset($product_data['id']);
        $data = array_merge($data, $product_data);
        if (!@$this->resource->is_while && ($this->type_action == 'single' /*|| strpos($request->route()->uri(), 'home_products') !== false*/)) {
            foreach (['sound', 'electronic'] as $index) {
                if ($this->{$index . "s"})
                    $data[$index . "s"] = $this->{$index . "s"}->map(function ($item) use($index) {
                        return [
                            "{$index}_id" => new ResourceData($item),
                            'link' => $item->pivot->link
                        ];
                    })->toArray();
            }
            foreach (["author", "translator", "collector", "editor", "cover_designer"] as $index) {
                try {
                    $creators = @$this->creators && @$this->creators?->count() ? $this->creators->where('pivot.group', $index) : collect();
                    $data["{$index}s"] = $creators && $creators?->count() ? iresourcedata('BookCreator')::collection($this->creators->where('pivot.group', $index)): [];
                }catch (\Throwable $exception) {}
            }
            if (@$data['translators'] && $data['translators']?->count() && $this->type_action == 'single')
                $data["translators_books"] = static::collection(imodal('Book')::whereHas('creators', function ($q) {
                    $q->whereIn('book_creators.id', $this->creators->where('pivot.group', 'translator')->pluck('id')->toArray());
                })->orderByRaw('RAND()')->limit(10)->get()->map(function ($item) {
                    $item->is_while = true;
                    return $item;
                }));
            if (@$data['authors'] && $data['authors']?->count() && $this->type_action == 'single')
                $data["authors_books"] = static::collection(imodal('Book')::whereHas('creators', function ($q) {
                    $q->whereIn('book_creators.id', $this->creators->where('pivot.group', 'author')->pluck('id')->toArray());
                })->orderByRaw('RAND()')->limit(10)->get()->map(function ($item) {
                    $item->is_while = true;
                    return $item;
                }));
            try {
                if (($book_index = $this->book_index ?: $this->resource->getFile('book_index')) && @$book_index['original']->slug) {
                    $data['book_index_b64'] = base64_encode(file_get_contents(public_path($book_index['original']->slug)));
                }
            } catch (\Throwable $exception) {}
        }
        if ($this->prices && ($price = $this->prices->where('stock', '>', 0)->first()))
            $data['price_sale'] = $price->price_sale;
        unset($data['product']);
        return $data;
    }
}
