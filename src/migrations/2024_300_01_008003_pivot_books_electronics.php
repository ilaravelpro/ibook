<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books_electronics', function (Blueprint $table) {
            $table->bigInteger('book_id')->unsigned();
            $table->bigInteger('electronic_id')->unsigned();
            $table->text('link')->nullable();
            $table->primary(['book_id', 'electronic_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('books_electronics');
    }
};
