<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note_Notecategories extends Model
{
    use HasFactory;

    protected $table = 'note_note_categories';

    protected $fillable = [
        'contentId',
        'noteCategoryId'
    ];
    
}
