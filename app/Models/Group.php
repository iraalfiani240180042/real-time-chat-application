<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    // MEMBER GROUP
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    // PESAN GROUP
    public function messages()
    {
        return $this->hasMany(GroupMessage::class);
    }
}