<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class AdminController extends Controller
{
    public function index ()
    {
        return view('admin.index');
    }

    public function brands ()
    {
        $brands = Brand::orderBy('id', 'DESC')->paginate(10); //Brand::latest()->paginate(10)  By Default{ ASC }
        return view('admin.brands', compact('brands'));
    }

    public function brand_create ()
    {
        return view('admin.brands-create');
    }

    public function brand_store ( Request $request )
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
        $image = $request->file('image');
        $file_extension = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' .$file_extension;
        $this->GenerateBrandThumbnailsImage($image, $file_name);
        $brand->image = $file_name;
        $brand->save();
        return redirect()->route('admin.brands')->with('status', 'Brand Created Successfully.');
    }

    public function brand_edit ( $id )
    {
        $brand = Brand::find($id);
        return view('admin.brand-edit', compact('brand'));
    }

    public function brand_update ( Request $request )
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug' . $request->id,
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $brand = Brand::find( $request->id );
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/brands/'. '/' . $brand->image))) {
                File::delete(public_path('uploads/brands/'. '/' . $brand->image));
            }
            $image = $request->file('image');
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' .$file_extension;
            $this->GenerateBrandThumbnailsImage($image, $file_name);
            $brand->image = $file_name;
        }

        $brand->save();
        return redirect()->route('admin.brands')->with('status', 'Brand Updated Successfully.');
    }

    public function GenerateBrandThumbnailsImage ( $image, $imageName )
    {
        $destinationPath = public_path('uploads/brands');
        $img = Image::read($image->path());
        $img->cover(124, 124, 'top');
        $img->resize(124, 124, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath . '/' . $imageName);
    }

    public function brand_destroy ( $id )
    {
        $brand = Brand::find($id);
        if (File::exists(public_path('uploads/brands/'. '/' . $brand->image))) {
            File::delete(public_path('uploads/brands/'. '/' . $brand->image));
        }
        $brand->delete();
        return redirect()->route('admin.brands')->with('status', 'Brand Deleted Successfully.');
    }

    public function categories ()
    {
        $categories = Category::orderBy('id', 'DESC')->paginate(10);
        return view('admin.categories', compact('categories'));
    }

    public function category_create ()
    {
        return view('admin.category-create');
    }

    public function category_store ( Request $request )
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $image = $request->file('image');
        $file_extension = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' .$file_extension;
        $this->GenerateCategoryThumbnailsImage($image, $file_name);
        $category->image = $file_name;
        $category->save();
        return redirect()->route('admin.categories')->with('status', 'Category Created Successfully.');
    }

    public function category_edit ( $id )
    {
        $category = Category::find($id);
        return view('admin.category-edit', compact('category'));
    }

    public function category_update ( Request $request )
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,' . $request->id,
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);


        $category = Category::find( $request->id );
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/categories/'. '/' . $category->image))) {
                File::delete(public_path('uploads/categories/'. '/' . $category->image));
            }
            $image = $request->file('image');
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' .$file_extension;
            $this->GenerateCategoryThumbnailsImage($image, $file_name);
            $category->image = $file_name;
        }

        $category->save();
        return redirect()->route('admin.categories')->with('status', 'Categories Updated Successfully.');
    }

    public function GenerateCategoryThumbnailsImage ( $image, $imageName )
    {
        $destinationPath = public_path('uploads/categories');
        $img = Image::read($image->path());
        $img->cover(124, 124, 'top');
        $img->resize(124, 124, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath . '/' . $imageName);
    }

    public function category_destroy ( $id )
    {
        $category = Category::find($id);
        if (File::exists(public_path('uploads/categories/'. '/' . $category->image))) {
            File::delete(public_path('uploads/categories/'. '/' . $category->image));
        }
        $category->delete();
        return redirect()->route('admin.categories')->with('status', 'Category Deleted Successfully.');
    }

    public function products ()
    {
        $products = Product::orderBy('created_at', 'DESC')->paginate(10);
        return view('admin.products', compact('products'));
    }

    public function product_create ()
    {
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();
        return view('admin.product-create', compact('categories', 'brands'));
    }

    public function product_store ( Request $request )
    {
        $request->validate([
            'name'  => 'required',
            'slug'  => 'required|unique:products,slug',
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU'   => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        $current_timestamp = Carbon::now()->timestamp;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $current_timestamp . '.' . $image->extension();
            $this->GenerateProductThumbnailsImage($image, $imageName);
            $product->image = $imageName;
        }

        $gallery_arr = array();
        $gallery_images = "";
        $counter = 1;

        if ($request->hasFile('images')) {
            $allowedFileExtension = ['jpg', 'png', 'jpeg', 'gif', 'svg'];
            $files = $request->file('images');

            foreach ($files as $file) {
                $gallery_extensions = $file->getClientOriginalExtension();
                $gallery_check = in_array($gallery_extensions, $allowedFileExtension);

                if ( $gallery_check ) {
                    $gallery_name = $current_timestamp . '_' . $counter . '.' . $gallery_extensions;
                    $this->GenerateProductThumbnailsImage($file, $gallery_name);
                    array_push($gallery_arr, $gallery_name);
                    $counter++;
                }
            }
            $gallery_images = implode(',', $gallery_arr);
        }
        $product->images = $gallery_images;
        $product->save();
        return redirect()->route('admin.products')->with('status', 'Product Added Successfully.');
    }

    public function product_edit ( $id )
    {
        $product = Product::find($id);
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();
        return view('admin.product-edit', compact('product', 'categories', 'brands'));
    }

    public function product_update ( Request $request )
    {
        $request->validate([
            'name'  => 'required',
            'slug'  => 'required|unique:products,slug,'.$request->id,
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU'   => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);

        $product = Product::find($request->id);
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        $current_timestamp = Carbon::now()->timestamp;

        if ($request->hasFile('image')) {

            if (File::exists(public_path('uploads/products/'. '/' . $product->image))) {
                File::delete(public_path('uploads/products/'. '/' . $product->image));
            }
            if (File::exists(public_path('uploads/products/thumbnails/'. '/' . $product->image))) {
                File::delete(public_path('uploads/products/thumbnails/'. '/' . $product->image));
            }
            $image = $request->file('image');
            $imageName = $current_timestamp . '.' . $image->extension();
            $this->GenerateProductThumbnailsImage($image, $imageName);
            $product->image = $imageName;
        }

        $gallery_arr = array();
        $gallery_images = "";
        $counter = 1;

        if ($request->hasFile('images')) {

            foreach(explode(',', $product->images) as $imgFile) {
                if (File::exists(public_path('uploads/products/'. '/' . $imgFile))) {
                    File::delete(public_path('uploads/products/'. '/' . $imgFile));
                }
                if (File::exists(public_path('uploads/products/thumbnails/'. '/' . $imgFile))) {
                    File::delete(public_path('uploads/products/thumbnails/'. '/' . $imgFile));
                }
            }

            $allowedFileExtension = ['jpg', 'png', 'jpeg', 'gif', 'svg'];
            $files = $request->file('images');

            foreach ($files as $file) {
                $gallery_extensions = $file->getClientOriginalExtension();
                $gallery_check = in_array($gallery_extensions, $allowedFileExtension);

                if ( $gallery_check ) {
                    $gallery_name = $current_timestamp . '_' . $counter . '.' . $gallery_extensions;
                    $this->GenerateProductThumbnailsImage($file, $gallery_name);
                    array_push($gallery_arr, $gallery_name);
                    $counter++;
                }
            }
            $gallery_images = implode(',', $gallery_arr);
            $product->images = $gallery_images;
        }
        $product->save();
        return redirect()->route('admin.products')->with('status', 'Product Updated Successfully.');

    }

    public function GenerateProductThumbnailsImage ( $image, $imageName )
    {
        $destinationThumbnailPath = public_path('uploads/products/thumbnails');
        $destinationPath = public_path('uploads/products');
        $img = Image::read($image->path());
        $img->cover(540, 689, 'top');
        $img->resize(540, 689, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath . '/' . $imageName);

        $img->resize(104, 104, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationThumbnailPath . '/' . $imageName);
    }

    public function product_destroy ( $id )
    {
        $product = Product::find($id);
        if (File::exists(public_path('uploads/products/'. '/' . $product->image))) {
            File::delete(public_path('uploads/products/'. '/' . $product->image));
        }
        if (File::exists(public_path('uploads/products/thumbnails/'. '/' . $product->image))) {
            File::delete(public_path('uploads/products/thumbnails/'. '/' . $product->image));
        }

        foreach(explode(',', $product->images) as $imgFile) {
            if (File::exists(public_path('uploads/products/'. '/' . $imgFile))) {
                File::delete(public_path('uploads/products/'. '/' . $imgFile));
            }
            if (File::exists(public_path('uploads/products/thumbnails/'. '/' . $imgFile))) {
                File::delete(public_path('uploads/products/thumbnails/'. '/' . $imgFile));
            }
        }

        $product->delete();
        return redirect()->route('admin.products')->with('status', 'Product Deleted Successfully.');
    }
}
