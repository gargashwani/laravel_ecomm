<?php
namespace App;

use App\City;
use App\User;
use App\State;
use App\Country;
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

    // User has one country
    public function country(){
        return $this->belongsTo('App\Country');
    }

    // User has one state
    public function state(){
        return $this->belongsTo('App\State');
    }

    // User has one city
    public function city(){
        return $this->belongsTo('App\City');
    }


}
