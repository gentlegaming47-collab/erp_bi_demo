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
        Schema::create('item_raw_material_mapping_details', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('item_id')->unsigned();
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');      


            $table->bigInteger('raw_material_id')->unsigned();
            $table->foreign('raw_material_id')->references('id')->on('raw_materials')->onDelete('cascade');      

            $table->float('raw_material_qty');

            

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
        Schema::dropIfExists('item_raw_material_mapping_details');
    }
};
