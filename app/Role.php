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
        // return $this->belongsToMany('App\Role', table_belongs_to ,'foreign_key', 'other_key');
        // return $this->belongsToMany('App\Role', 'role_user_table', 'user_id', 'role_id');
        return $this->hasMany('App\Role');
    }
}
