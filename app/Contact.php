<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'firstname',
        'lastname',
        'contact_number',
        // 'country',
    ];

    protected $appends = [
        'fullname',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getFullnameAttribute()
    {
        return "{$this->firstname} {$this->lastname}";
    }

    public function messages()
    {
        return $this->hasMany('App\Message', 'from');
    }

}
