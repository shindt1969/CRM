<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNoteContentToNoteCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('note_content_to_note_categories', function (Blueprint $table) {
            // $table->id();
            $table->primary(['contentId', 'noteCategoryId'], "ContentCategoryId");
            $table->unsignedBigInteger('contentId');
            $table->unsignedBigInteger('noteCategoryId');
            $table->timestamps();

            $table->foreign('contentId')->references('id')->on('noteContents')->onDelete('cascade');
            $table->foreign('noteCategoryId')->references('id')->on('note_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('note_content_to_note_categories');
    }
}
