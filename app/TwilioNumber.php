<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class TwilioNumber extends Model
{
    protected $fillable = [
        'contact_number',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
