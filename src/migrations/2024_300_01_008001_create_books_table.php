<?php


/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 4/3/20, 7:49 PM
 * Copyright (c) 2020. Powered by iamir.net
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('id_old')->nullable();
            $table->bigInteger('product_id')->nullable()->unsigned();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->bigInteger('publisher_id')->nullable()->unsigned();
            $table->foreign('publisher_id')->references('id')->on('publishers')->onDelete('cascade');
            $table->bigInteger('size_id')->nullable()->unsigned();
            $table->foreign('size_id')->references('id')->on('book_sizes')->onDelete('cascade');
            $table->bigInteger('cover_id')->nullable()->unsigned();
            $table->foreign('cover_id')->references('id')->on('book_covers')->onDelete('cascade');
            $table->bigInteger('book_index_id')->nullable()->unsigned();
            $table->foreign('book_index_id')->references('id')->on('posts')->onDelete('cascade');
            $table->string('title_latin')->nullable();
            $table->string('isbn')->nullable();
            $table->string('book_id')->nullable();
            $table->string('book_lang')->nullable();
            $table->integer('width_per_page')->nullable();
            $table->bigInteger('count_page')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('books');
    }
};
