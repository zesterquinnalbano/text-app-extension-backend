<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class TwilioNumber extends Model
{
    protected $fillable = [
        'contact_number',
        'user_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
