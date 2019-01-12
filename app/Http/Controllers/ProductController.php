<?php
namespace App\Http\Controllers;
use App\Category;
use App\Http\Requests\StoreProduct;
use App\Product;
use App\CategoryParent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Cart;
use Session;



class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::with('categories')->paginate(3);
        return view('admin.products.index', compact('products'));
    }
/**
     * Display Trashed listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trash()
    {
        $products = Product::with('categories')->onlyTrashed()->paginate(3);
        return view('admin.products.index', compact('products'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::with('childrens')->get();
        return view('admin.products.create', compact('categories'));
    }
        /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
    //    dd(Session::get('cart'));
       $categories = Category::with('childrens')->get();
       $products = Product::with('categories')->paginate(3);
       return view('products.all', compact('categories','products'));
    }

    public function single(Product $product){
      return view('products.single', compact('product'));
    }
    public function addToCart(Product $product, Request $request){
        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $qty = $request->qty ? $request->qty : 1;
        $cart = new Cart($oldCart);
        $cart->addProduct($product, $qty);
        Session::put('cart', $cart);
        return back()->with('message', "Product $product->title has been successfully added to Cart");
    }
    public function cart(){
      if(!Session::has('cart')){
        return view('products.cart');
      }
      $cart = Session::get('cart');
      return view('products.cart', compact('cart'));
    }
   public function removeProduct(Product $product){
      $oldCart = Session::has('cart') ? Session::get('cart') : null;
      $cart = new Cart($oldCart);
      $cart->removeProduct($product);
      Session::put('cart', $cart);
      return back()->with('message', "Product $product->title has been successfully removed From the Cart");
   }
    public function updateProduct(Product $product, Request $request){

      $oldCart = Session::has('cart') ? Session::get('cart') : null;
      $cart = new Cart($oldCart);
      $cart->updateProduct($product, $request->qty );
      Session::put('cart', $cart);
      return back()->with('message', "Product $product->title has been successfully Updated in the Cart");
   }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProduct $request)
    {
        // dd($request->extras);
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
       $path = $request->thumbnail->storeAs('images', $name, 'public');
        //    $path = $request->thumbnail->move(public_path('images'), $name);
     }
       $product = Product::create([
           'title'=>$request->title,
           'slug' => $request->slug,
           'description'=>$request->description,
           'thumbnail' => $path,
           'status' => $request->status,
           'options' => isset($request->extras) ? json_encode($request->extras) : null,
           'featured' => ($request->featured) ? $request->featured : 0,
           'price' => $request->price,
           'discount'=>$request->discount ? $request->discount : 0,
           'discount_price' => ($request->discount_price) ? $request->discount_price : 0,
       ]);
       if($product){
            $product->categories()->attach($request->category_id,['created_at'=>now(), 'updated_at'=>now()]);
            return redirect(route('admin.product.index'))->with('message', 'Product Successfully Added');
       }else{
            return back()->with('message', 'Error Inserting Product');
       }
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
       $categories = Category::with('childrens')->get();
       return view('admin.products.create',compact('product', 'categories'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(StoreProduct $request, Product $product)
    {
        if($request->has('thumbnail')){
            Storage::delete($product->thumbnail);
           $extension = ".".$request->thumbnail->getClientOriginalExtension();
           $name = basename($request->thumbnail->getClientOriginalName(), $extension).time();
           $name = $name.$extension;
           $path = $request->thumbnail->storeAs('images', $name);
           $product->thumbnail = $path;
         }
        $product->title =$request->title;
        //$product->slug = $request->slug;
        $product->description = $request->description;
        $product->status = $request->status;
        $product->featured = ($request->featured) ? $request->featured : 0;
        $product->price = $request->price;
        $product->discount = $request->discount ? $request->discount : 0;
        $product->discount_price = ($request->discount_price) ? $request->discount_price : 0;
        $product->categories()->detach();

        if($product->save()){
            $product->categories()->attach($request->category_id, ['created_at'=>now(), 'updated_at'=>now()]);
            return redirect(route('admin.product.index'))->with('message', "Product Successfully Updated!");
        }else{
            return back()->with('message', "Error Updating Product");
        }
    }
 public function recoverProduct($id)
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
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
          if($product->categories()->detach() && $product->forceDelete()){
            //   Storage::delete() is used to delete image from folder
            //  disk('public') is used to specify that the path is public/storage
            Storage::disk('public')->delete($product->thumbnail);
            return back()->with('message','Product Successfully Deleted!');
        }else{
            return back()->with('message','Error Deleting Product');
        }
    }
        /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function remove(Product $product)
    {
        if($product->delete()){
            return back()->with('message','Product Successfully Trashed!');
        }else{
            return back()->with('message','Error Deleting Product');
        }
    }
}
