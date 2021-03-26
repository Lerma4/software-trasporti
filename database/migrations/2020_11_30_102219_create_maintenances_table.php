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
        Schema::create('maint-alreadyDone', function (Blueprint $table) {
            $table->id();
            $table->string('plate');
            $table->string('type');
            $table->date('date');
            $table->bigInteger('km')->nullable()->default(NULL);
            $table->string('garage')->nullable()->default(NULL);
            $table->bigInteger('price')->nullable()->default(NULL);
            $table->string('notes')->nullable()->default(NULL);
            $table->bigInteger('companyId');
        });

        Schema::create('maint-stillToDo', function (Blueprint $table) {
            $table->id();
            $table->string('plate');
            $table->string('type');
            $table->float('km', 8, 0);
            $table->bigInteger('renew')->nullable()->default(NULL);
            $table->string('notes')->nullable()->default(NULL);
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
        Schema::dropIfExists('maint-alreadyDone');
        Schema::dropIfExists('maint-stillToDo');
    }
}
