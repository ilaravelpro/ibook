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
        Schema::create('publishers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('creator_id')->nullable()->unsigned();
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('logo_id')->nullable()->unsigned();
            $table->foreign('logo_id')->references('id')->on('posts')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->string('website')->nullable();
            $table->string('template')->nullable();
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->integer('order')->default(0);
            $table->string('local')->nullable();
            $table->string('status')->default('active');
            $table->timestamp('approved_at')->nullable();
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
        Schema::dropIfExists('publishers');
    }
};
