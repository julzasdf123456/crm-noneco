<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCRMServiceConnections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('CRM_ServiceConnections', function (Blueprint $table) {
            $table->string('id')->unsigned();
            $table->primary('id');
            $table->string('MemberConsumerId')->nullable();
            $table->date('DateOfApplication')->nullable();
            $table->string('ServiceAccountName')->nullable();
            $table->integer('AccountCount')->nullable();
            $table->string('Sitio', 1000)->nullable();
            $table->string('Barangay', 10)->nullable();
            $table->string('Town', 10)->nullable();
            $table->string('ContactNumber', 500)->nullable();
            $table->string('EmailAddress', 800)->nullable();
            $table->string('AccountType', 100)->nullable();
            $table->string('AccountOrganization', 100)->nullable();
            $table->string('OrganizationAccountNumber', 100)->nullable();
            $table->string('IsNIHE')->nullable();
            $table->string('AccountApplicationType', 100)->nullable();
            $table->string('ConnectionApplicationType', 100)->nullable();
            $table->string('Status', 100)->nullable();
            $table->string('Notes', 2000)->nullable();
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
        Schema::dropIfExists('CRM_ServiceConnections');
    }
}
