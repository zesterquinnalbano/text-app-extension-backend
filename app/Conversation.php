<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'twilio_number_id',
        'contact_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function message()
    {
        return $this->hasOne('App\Message', 'conversation_id');
    }

    public function messages()
    {
        return $this->hasMany('App\Message', 'conversation_id');
    }

    public function contact()
    {
        return $this->belongsTo('App\Contact', 'contact_id');
    }

    public function twilioNumber()
    {
        return $this->belongsTo('App\TwilioNumber', 'twilio_number_id');
    }
}
