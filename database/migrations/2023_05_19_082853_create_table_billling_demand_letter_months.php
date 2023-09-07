<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableBilllingDemandLetterMonths extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Billing_DemandLetterMonths', function (Blueprint $table) {
            $table->string('id')->unsigned();
            $table->primary('id');
            $table->string('DemandLetterId');
            $table->date('ServicePeriod')->nullable();
            $table->string('AccountNumber')->nullable();
            $table->decimal('NetAmount', 12, 2)->nullable();
            $table->decimal('Surcharge', 12, 2)->nullable();
            $table->decimal('Interest', 12, 2)->nullable();
            $table->decimal('TotalAmountDue', 12, 2)->nullable();
            $table->string('Notes', 1000)->nullable();
            $table->string('Status')->nullable();
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
        Schema::dropIfExists('Billing_DemandLetterMonths');
    }
}
