<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoteContent extends Model
{
    use HasFactory;

    protected $table = 'noteContents';

    protected $fillable = [
        'text',
        'owner_id',
        'type_id',
        'create_by_id',
    ];
}
