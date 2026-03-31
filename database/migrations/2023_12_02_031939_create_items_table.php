<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
          
            $table->bigInteger('item_group_id')->unsigned();
            $table->foreign('item_group_id')->references('id')->on('item_groups')->onDelete('cascade');      

            $table->bigInteger('item_sequence');

            $table->string('item_code');

            $table->bigInteger('unit_id')->unsigned();
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');      

            
            $table->float('min_stock_qty');

            $table->float('max_stock_qty');

            $table->float('re_order_qty');

            $table->bigInteger('hsn_code')->unsigned();
            $table->foreign('hsn_code')->references('id')->on('hsn_code')->onDelete('cascade');      
            $table->float('rate_per_unit');

            $table->enum('require_raw_material_mapping', ['yes', 'no']);

            $table->enum('fitting_item', ['yes', 'no']);
            


            $table->bigInteger('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');      

            $table->bigInteger('created_by_user_id')->unsigned();
            $table->foreign('created_by_user_id')->references('id')->on('admin')->onDelete('cascade');   

            $table->timestamp('created_on')->nullable();       
                        
            $table->bigInteger('last_by_user_id')->unsigned();
            $table->foreign('last_by_user_id')->references('id')->on('admin')->onDelete('cascade')->nullable();     

            $table->timestamp('last_on')->nullable();
               
            
            $table->bigInteger('locked_by_user_id')->unsigned();
            $table->foreign('locked_by_user_id')->references('id')->on('admin')->onDelete('cascade')->nullable();   
            
            $table->timestamp('locked_on')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
};
