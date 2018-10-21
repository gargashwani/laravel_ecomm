<?php
namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    //
    protected $guarded = [];

    public function getRouteKeyName(){
        return 'slug';
    }

    public function users(){
        return $this->belongsToMany('App\User');
    }
}
