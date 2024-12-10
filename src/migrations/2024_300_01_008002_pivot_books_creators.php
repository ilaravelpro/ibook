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
        Schema::create('books_creators', function (Blueprint $table) {
            $table->bigInteger('book_id')->unsigned();
            $table->bigInteger('creator_id')->unsigned();
            $table->string('group')->nullable();
            $table->primary(['book_id', 'creator_id'/*, 'group'*/]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('books_creators');
    }
};
