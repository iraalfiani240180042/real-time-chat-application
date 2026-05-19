<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'group_id',
        'sender_id',
        'message'
    ];

    // =========================
    // SENDER
    // =========================
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // =========================
    // PRIVATE CONVERSATION
    // =========================
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    // =========================
    // GROUP CHAT
    // =========================
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}