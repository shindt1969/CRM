<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('contents')){
            Schema::create('contents', function (Blueprint $table) {
                $table->id();
                $table->text('text');
                $table->unsignedBigInteger('owner_id');
                $table->unsignedBigInteger('type_id');
                $table->unsignedInteger('create_by_id');
                $table->timestamps();
    
                $table->foreign('type_id')->references('id')->on('content_types');
                $table->foreign('create_by_id')->references('id')->on('users');
            });
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contents');
    }
}
