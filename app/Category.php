<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Product;
use App\CategoryParent;

class Category extends Model
{
     use SoftDeletes;
    //because all the fields are guarded by default, so we are making them free from guard
     protected $guarded = [];
     protected $dates = ['deleted_at'];

    //  one cat can have many products
     public function products(){
    	return $this->belongsToMany('App\Product', 'category_product');
    }

    //  one cat can have many child categories
    // belongsToMany Syntax:
    //  $this->belongsToMany('Current class Model', Pivot_table, fk, pk);
    public function childrens(){
        // here parent_id is fk for childrens
// category_id is primary key for childres,
// when we get the children using this Category Model,
// this method will work
        return $this->belongsToMany(Category::class,'category_parent','parent_id','category_id');
    }

    //  one cat can have many parent categories
    public function parents(){
        return $this->belongsToMany(Category::class,'category_parent','category_id','parent_id');
    }

    // this is to define the slug as the id to the links of categories
	// public function getRouteKeyName(){
   	//  return 'slug';
	// }
}
