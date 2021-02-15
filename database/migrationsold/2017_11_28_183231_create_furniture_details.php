<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFurnitureDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('furniture_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('school_id');
            $table->string('user_id');
            $table->string('category');
            $table->string('sub_category');
            $table->string('item_name');
            $table->decimal('purchaserate');
            $table->decimal('distribute_rate');
            $table->integer('quantity');
            $table->integer('avail_quantity');
            $table->string('amount');
            $table->string('comment');
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('furniture_details');
    }
}
