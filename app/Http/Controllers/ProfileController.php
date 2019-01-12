<?php
namespace App\Http\Controllers;
use App\City;
use App\Country;
use App\Http\Requests\StoreUserProfile;
use App\Profile;
use App\Role;
use App\State;
use App\User;
use Illuminate\Http\Request;
class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::with('role','profile')->paginate(3);
        // dd($users);
        return view('admin.users.index',compact('users'));
    }
/**
     * Display Trashed listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trash()
    {
        // $users = User::onlyTrashed('role','profile')->paginate(3);
        $users = User::onlyTrashed()->get();
        return view('admin.users.index', compact('users'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::all();
        $countries = Country::all();
        return view('admin.users.create',compact('roles','countries'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserProfile $request)
    {
        $path = 'images/profile/no-thumbnail.jpeg';
        if($request->has('thumbnail')){
            $extension = ".".$request->thumbnail->getClientOriginalExtension();
            $name = basename($request->thumbnail->getClientOriginalName(), $extension).time();
            $name = $name.$extension;
            $path = $request->thumbnail->storeAs('images/profile', $name, 'public');
        }
       $user = User::create([
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'status' => $request->status,
       ]);
       if($user){
        $profile = Profile::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'thumbnail' => $path,
            'country_id' => $request->country_id,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'phone' => $request->phone,
            'slug' => $request->slug,
        ]);
       }
       if($user && $profile)
            return redirect(route('admin.profile.index'))->with('message','User Created Successfully');
        else
            return back()->with('message', 'Error Inserting new User');
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function show(Profile $profile)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function edit(Profile $profile)
    {
        $user = User::find($profile)->first();
        $roles = Role::all();
        return view('admin.users.create',compact('user','roles'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Profile $profile)
    {
        //
    }
    public function recoverProfile($id)
    {
        $product = Product::with('categories')->onlyTrashed()->findOrFail($id);
        if($product->restore())
            return back()->with('message','Product Successfully Restored!');
        else
            return back()->with('message','Error Restoring Product');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function destroy(Profile $profile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function remove(Profile $profile)
    {
        // dd($profile->user_id);
        // $user_id = $profile->user_id;

        // $delete_user = User::find($user_id);

        // if($profile->delete() && $delete_user->delete()){
        if($profile->user->delete()){
            return back()->with('message','User Successfully Trashed!');
        }else{
            return back()->with('message','Error Deleting Product');
        }
    }


    public function getStates(Request $request, $id){
        if($request->ajax())
            return State::where('country_id', $id)->get();
        else{
            return 0;
        }
    }
    public function getCities(Request $request, $id){
        if($request->ajax())
            return City::where('state_id', $id)->get();
        else{
            return 0;
        }
    }
}
