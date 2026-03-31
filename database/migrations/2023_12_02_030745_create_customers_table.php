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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            
            $table->bigInteger('customer_group_id')->unsigned();
            $table->foreign('customer_group_id')->references('id')->on('customer_groups')->onDelete('cascade');      

            $table->string('address');

          

            $table->bigInteger('village_id')->unsigned();
            $table->foreign('village_id')->references('id')->on('villages')->onDelete('cascade');


            $table->integer('pincode');

            $table->string('mobile_no');

            $table->string('email');

            $table->string('PAN');

            $table->string('GSTIN');

            $table->string('aadhar_no');

            
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
        Schema::dropIfExists('customers');
    }
};
