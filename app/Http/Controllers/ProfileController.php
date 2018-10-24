<?php

namespace App\Http\Controllers;

use App\City;
use App\Role;
use App\User;
use App\State;
use App\Country;
use App\Profile;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserProfile;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::with('role','profile')->paginate('3');
        // dd($users);
        return view('admin.users.index')->with(['users'=>$users]);
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
        // dd($request->all());

        $path = 'images/no-thumbnail.jpeg';
        if($request->has('thumbnail')){
         $extension = ".".$request->thumbnail->getClientOriginalExtension();
         $name = basename($request->thumbnail->getClientOriginalName(), $extension).time();
         $name = $name.$extension;

          // if for any how, images not shown in frontend, run the below command in CLI
          // By default Laravel puts files in the storage/app/public directory, which is not accessible from the outside web. So you have to create a symbolic link between that directory and your public one:
          // storage/app/public -> public/storage
          // You can do that by executing the
          // php artisan storage:link
          // https://laravel.com/docs/5.4/filesystem#the-public-disk
         $path = $request->thumbnail->storeAs('images/profile', $name, 'public');

        }

        $user = User::create([
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'status' => $request->status
        ]);

        if($user){
            $profile = Profile::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'phone' => $request->phone,
                'thumbnail' => $path,
                'slug' => $request->slug,
            ]);
        }

        if($user && $profile){
            return redirect(route('admin.profile.index'))->with('message', 'User Successfully Added');
        }else{
            return back()->withInput()->with('message', 'Error inserting new user!');
        }

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
        //
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
