<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->string('user_email');
            $table->string('name');
            $table->date('date');
            $table->integer('type');
            $table->string('plate');
            $table->string('plate_s')->nullable();
            $table->string('container')->nullable();
            $table->string('garage')->nullable();
            $table->string('start');
            $table->string('destination');
            $table->string('stops')->nullable();
            $table->float('km');
            $table->float('distance');
            $table->float('fuel');
            $table->float('cost');
            $table->text('note')->nullable();
            $table->bigInteger('companyId');
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
        Schema::dropIfExists('trips');
    }
}
