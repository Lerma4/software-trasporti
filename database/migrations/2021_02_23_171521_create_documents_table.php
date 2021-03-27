<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('user_email');
            $table->string('user_name');
            $table->string('note')->nullable();
            $table->bigInteger('companyId');
            $table->timestamps();
        });

        Schema::create('documents_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')
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
        Schema::dropIfExists('documents_files');
        Schema::dropIfExists('documents');
    }
}
