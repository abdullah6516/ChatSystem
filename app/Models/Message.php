<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['from', 'to', 'message', 'type'];

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to');
    }
}
