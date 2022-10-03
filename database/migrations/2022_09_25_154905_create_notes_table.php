<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('notes')){
            Schema::create('notes', function (Blueprint $table) {
                $table->id();
                $table->text('text');
                $table->unsignedBigInteger('target_id');
                $table->unsignedBigInteger('target_type_id');
                $table->unsignedInteger('create_by_id');
                $table->timestamps();
    
                $table->foreign('create_by_id')->references('id')->on('users');
            });

            Schema::table('notes', function (Blueprint $table) {
                $table->softDeletes();
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
        Schema::dropIfExists('notes');
    }
}
