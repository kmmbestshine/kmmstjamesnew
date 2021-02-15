<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpenditureDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenditure_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('school_id');
            $table->string('user_id');
            $table->string('name');
            $table->date('date');
            $table->string('purpose');
            $table->string('category',250);
            $table->string('descrption',750);
            $table->string('quantity',75);
            $table->string('comment',250);
            $table->decimal('amount');
            $table->string('approved_by',250);
            $table->string('given_by',250);
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
        Schema::table('expenditure_details', function (Blueprint $table) {
            //
        });
    }
}
