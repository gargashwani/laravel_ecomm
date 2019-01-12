<?php
namespace App;
use App\Role;
use App\Profile;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'email', 'password','role_id'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    /**
     * Get the role record associated with the user.
     */
    public function role()
    {
        return $this->belongsTo('App\Role');
    }
    public function profile(){
        return $this->hasOne('App\Profile');
    }
    // to create a short name to print country name
    public function getCountry(){
        return $this->profile->country->name;
    }
    // to create a short name to print state name
    public function getState(){
        return $this->profile->state->name;
    }
    // to create a short name to print city name
    public function getCity(){
        return $this->profile->city->name;
    }
}
