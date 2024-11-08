<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarketplaceProductFlagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marketplace_product_flags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->text('reason');
            $table->boolean('is_owner')->default(0);
            $table->integer('product_id')->unsigned();
            $table->integer('seller_id')->unsigned()->nullable();
            $table->foreign('product_id')->references('id')->on('marketplace_products')->onDelete('cascade');
            $table->foreign('seller_id')->references('id')->on('marketplace_sellers')->onDelete('set null');
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
        Schema::dropIfExists('marketplace_product_flags');
    }
}
