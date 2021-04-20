<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrashesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crashes', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('email');
            $table->string('name');
            $table->string('plate');
            $table->string('plate_s')->nullable();
            $table->text('description');
            $table->bigInteger('companyId');
            $table->timestamps();
        });

        Schema::create('crashes_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crash_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade');
            $table->string('filename');
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
        Schema::dropIfExists('crashes_photos');
        Schema::dropIfExists('crashes');
    }
}
