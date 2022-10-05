<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\NoteCategories;

class Note extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'text',
        'target_id',
        'target_type_id',
        'create_by_id',
    ];

    public function noteCategories()
    {
        return $this->belongsToMany(NoteCategories::class, 'note_note_categories', 'contentId', 'noteCategoryId');
    }
}
