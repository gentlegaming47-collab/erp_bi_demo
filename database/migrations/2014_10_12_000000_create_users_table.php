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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('user_name');            
            $table->string('password');
            $table->string('person_name');
            $table->string('mobile_no');            
            $table->string('email_id')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->enum('user_type', ['operator', 'state_manager', 'zonal_manager', 'director']);
            $table->enum('status', ['active', 'deactive']);
         
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
        Schema::dropIfExists('users');
    }
};
