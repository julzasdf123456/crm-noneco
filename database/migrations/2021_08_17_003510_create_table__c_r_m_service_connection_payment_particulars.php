<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCRMServiceConnectionPaymentParticulars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('CRM_ServiceConnectionPaymentParticulars', function (Blueprint $table) {
            $table->string('id')->unsigned();
            $table->primary('id');
            $table->string('Particular', 500)->nullable();
            $table->string('Description', 800)->nullable();
            $table->string('DefaultAmount', 500)->nullable();
            $table->string('VatPercentage', 50)->nullable();
            $table->string('Notes', 1000)->nullable();
            $table->string('AccountNumber')->nullable();
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
        Schema::dropIfExists('CRM_ServiceConnectionPaymentParticulars');
    }
}
