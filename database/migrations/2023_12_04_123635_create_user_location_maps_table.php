<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLocationMapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_location_maps', function (Blueprint $table) {
            $table->id();
          
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');      

            $table->bigInteger('location_id')->unsigned();
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');      

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
        Schema::dropIfExists('user_location_maps');
    }
}
