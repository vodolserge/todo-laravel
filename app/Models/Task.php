<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'completed',
        'parent_id',
        'user_id',
        'status',
        'priority',
        'description',
        'created_at',
        'completed_at'
    ];


    public function children()
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
