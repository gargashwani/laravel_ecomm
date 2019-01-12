<?php
namespace App\Http\Controllers;
use App\Category;
use Illuminate\Http\Request;
class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::paginate(3);
        return view('admin.categories.index', compact('categories'));
    }
    /**
     * Display Trashed listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trash()
    {
        $categories = Category::onlyTrashed()->paginate(3);
        return view('admin.categories.index', compact('categories'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.categories.create',compact('categories'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'=>'required|min:5',
            'slug'=>'required|min:5|unique:categories'
        ]);
        // this will add only given cols in the database table
        $categories = Category::create($request->only('title','description','slug'));
        // Many To Many Relationships
        // Attaching / Detaching
        // Eloquent also provides a few additional helper methods to make working with related models more convenient.
        // For example, let's imagine a user can have many roles and a role can have many users.
        // To attach a role to a user by inserting a record in the intermediate table that joins the models,
        // use the attach method:
        // dd($request->parent_id[0]);
        $categories->parents()->attach($request->parent_id,['created_at'=>now(), 'updated_at'=>now()]);
        return back()->with('message','Category Added Successfully!');
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return $category;
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    //  here we are passing Category modal in the method using dependency injection
    public function edit(Category $category)
    {
        // $category = Category::find($category);
        // if (!$category) {
        //     return redirect()->route('admin.categories.index')->withErrors([trans('errors.category_not_found')]);
        // }
        // else
        // {
        //     echo 'ok';
        // }
        // echo $category->id."<br/>";
        // here we are getting all categories list from the database,
        // but we don't want to add the current edited category in that list
        // therefore exclude current category, which is current modal object
         $categories = Category::where('id','!=', $category)->get();
        //  dd($categories);
        // return "This is category edit page";
         return view('admin.categories.create',['categories' => $categories, 'category'=>$category]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'title'=>'required|min:5',
            'slug'=>'required|min:5|unique:categories'
        ]);
        $category->title = $request->title;
        $category->description = $request->description;
        $category->slug = $request->slug;
        //detach all parent categories
        $category->parents()->detach();
        //attach selected parent categories
        $category->parents()->attach($request->parent_id,['created_at'=>now(), 'updated_at'=>now()]);
        //save current record into database
        $saved = $category->save();
        //return back to the /add/edit form
        if($saved)
            return back()->with('message','Record Successfully Updated!');
        else
            return back()->with('message', 'Error Updating Category');
    }
    public function recoverCat($id)
    {
        // Include trashed records when retrieving results...
        // $orders = App\Order::withTrashed()->search('Star Trek')->get();
        // Only include trashed records when retrieving results...
        // $orders = App\Order::onlyTrashed()->search('Star Trek')->get();
        $category = Category::onlyTrashed()->findOrFail($id);
        if($category->restore())
            return back()->with('message','Category Successfully Restored!');
        else
            return back()->with('message','Error Restoring Category');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        // this method deletes the cat from the database permanently
        if($category->childrens()->detach() && $category->forceDelete()){
            return back()->with('message','Category Successfully Deleted!');
        }else{
            return back()->with('message','Error Deleting Record');
        }
    }
    public function fetchCategories($id = 0){
        if($id == 0)
            return Category::all();
      $category =  Category::where('id', $id)->first();
      return $category->childrens;
    }
    /**
     * Remove the specified resource to trash.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function remove(Category $category)
    {
        // soft delete category
        if($category->delete()){
            return back()->with('message','Category Successfully Trashed!');
        }else{
            return back()->with('message','Error Deleting Record');
        }
    }
}
