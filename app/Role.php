<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\User;

class Role extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    //
    protected $guarded = [];

    public function users()
    {
        return $this->hasMany('App\User');
    }
}
