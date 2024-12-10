<?php
/*
 * Author: Amirhossein Jahani | iAmir.net
 * Email: me@iamir.net
 * Mobile No: +98-9146941147
 * Last modified: 2021/08/29 Sun 04:42 PM IRDT
 * Copyright (c) 2020-2022. Powered by iAmir.net
 */

Route::namespace('v1')->prefix('v1')->middleware('authIf:api')->group(function () {
    Route::apiResource('publishers', 'PublisherController', ['as' => 'api']);
    Route::apiResource('books', 'BookController', ['as' => 'api']);
    Route::post('books/{record}/favorite', 'BookController@favorite')->name('api.books.favorite');
    Route::apiResource('warehouses', 'WarehouseController', ['as' => 'api']);
    Route::apiResource('book_sizes', 'BookSizeController', ['as' => 'api']);
    Route::apiResource('book_covers', 'BookCoverController', ['as' => 'api']);
    Route::apiResource('book_electronics', 'BookElectronicController', ['as' => 'api']);
    Route::apiResource('book_sounds', 'BookSoundController', ['as' => 'api']);
    Route::apiResource('book_creators', 'BookCreatorController', ['as' => 'api']);
});
