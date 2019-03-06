<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class ContactGroup extends Model
{
    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function contacts()
    {
        return $this->hasMany('App\Contact', 'contact_group_id');
    }
}
