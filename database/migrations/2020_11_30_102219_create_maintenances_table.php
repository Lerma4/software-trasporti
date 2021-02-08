<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->string('plate');
            $table->string('type');
            $table->string('garage')->nullable()->default(NULL);
            $table->string('description')->nullable()->default(NULL);
            $table->date('date');
            $table->boolean('alert')->nullable()->default(FALSE);
            $table->bigInteger('km')->nullable()->default(NULL);
            $table->bigInteger('period')->nullable()->default(NULL);
            $table->bigInteger('price')->nullable()->default(NULL);
            $table->bigInteger('companyId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maintenances');
    }
}
