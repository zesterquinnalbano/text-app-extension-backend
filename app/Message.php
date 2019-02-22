<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'direction',
        'message',
        'created_at',
        'updated_at',
        'sent_by',
        'status',
        'new_message',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'new_message',
    ];

    protected $touches = ['conversation'];

    public function user()
    {
        return $this->belongsTo('App\User', 'sent_by');
    }

    public function conversation()
    {
        return $this->belongsTo('App\Conversation', 'conversation_id');
    }

}
