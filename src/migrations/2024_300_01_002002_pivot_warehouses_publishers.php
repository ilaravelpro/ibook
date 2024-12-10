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
        Schema::create('warehouses_publishers', function (Blueprint $table) {
            $table->bigInteger('warehouse_id')->unsigned();
            $table->bigInteger('publisher_id')->unsigned();
            $table->primary(['warehouse_id', 'publisher_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warehouses_publishers');
    }
};
