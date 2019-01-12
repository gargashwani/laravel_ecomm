<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\User;

class Role extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    //because all the fields are guarded by default, so we are making them free from guard
    protected $guarded = [];

    public function users()
    {
        return $this->hasMany('App\User');
    }
}
