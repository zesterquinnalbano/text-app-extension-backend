<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'firstname',
        'lastname',
        'contact_number',
        'created_by',
        'contact_group_id',
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

    public function contactGroup()
    {
        return $this->belongsTo('App\ContactGroup', 'contact_group_id');
    }
}
