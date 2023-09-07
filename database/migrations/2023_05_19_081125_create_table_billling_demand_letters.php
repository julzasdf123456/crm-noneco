<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableBilllingDemandLetters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Billing_DemandLetters', function (Blueprint $table) {
            $table->string('id')->unsigned();
            $table->primary('id');
            $table->string('AccountNumber')->nullable();
            $table->string('UserId')->nullable();
            $table->string('Status')->nullable();
            $table->date('DateSent')->nullable();
            $table->string('Notes', 1000)->nullable();
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
        Schema::dropIfExists('Billing_DemandLetters');
    }
}
