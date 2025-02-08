<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;
    // kolom selain id dapat di isi/diganti datanya
    protected $guarded = ['id'];

    protected $with = ['user', 'list'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function list()
    {
        return $this->belongsTo(TaskLists::class, 'list_id', 'id');
    }
}
