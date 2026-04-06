<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Str;
use DataTables;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::select(['id','title','price','status','image','category_id','show_in_menu','stock_status', 'sku_ref'])
            ->with('category')
            ->when($request->category_id, function($q) use ($request) {
                $q->where('category_id', $request->category_id);
            })
            ->when($request->product_type, function($q) use ($request) {
                if ($request->product_type === 'main') {
                    $q->where('status', 1);
                } elseif ($request->product_type === 'sub') {
                    $q->where('status', 0);
                }
            })
            ->latest();
            return DataTables::of($products)
                ->addIndexColumn()
                ->addColumn('price', function($row){
                    return '£'.$row->price;
                })
                ->addColumn('category_name', function($row){
                    return $row->category->name ?? '-';
                })
                ->addColumn('image', function($row){
                    $src = $row->image ? asset($row->image) : asset('/placeholder.webp');
                    return '<img src="'.$src.'" class="img-thumbnail img-fluid" width="200">';
                })
                ->addColumn('status', function($row){
                    $checked = $row->status == 1 ? 'checked' : '';
                    return '<div class="form-check form-switch" dir="ltr">
                                <input type="checkbox" class="form-check-input toggle-status" 
                                       id="customSwitchStatus'.$row->id.'" data-id="'.$row->id.'" '.$checked.'>
                                <label class="form-check-label" for="customSwitchStatus'.$row->id.'"></label>
                            </div>';
                })
                ->addColumn('stock_status', function($row){
                    $badge = $row->stock_status === 'in_stock' ? 'success' : 'danger';
                    $text = $row->stock_status === 'in_stock' ? 'In Stock' : 'Out of Stock';
                    $checked = $row->stock_status === 'in_stock' ? 'checked' : '';
                    return '<div class="form-check form-switch" dir="ltr">
                                <input type="checkbox" class="form-check-input toggle-stock-status" 
                                    id="customSwitchStock'.$row->id.'" data-id="'.$row->id.'" data-status="'.$row->stock_status.'" '.$checked.'>
                                <label class="form-check-label" for="customSwitchStock'.$row->id.'">
                                    <span class="badge bg-'.$badge.'">'.$text.'</span>
                                </label>
                            </div>';
                })
                ->addColumn('sidebar', function($row){
                    $checked = $row->show_in_menu == 1 ? 'checked' : '';
                    return '<div class="form-check form-switch" dir="ltr">
                                <input type="checkbox" class="form-check-input toggle-sidebar" 
                                       id="customSwitchSidebar'.$row->id.'" data-id="'.$row->id.'" '.$checked.'>
                                <label class="form-check-label" for="customSwitchSidebar'.$row->id.'"></label>
                            </div>';
                })
                ->addColumn('action', function($row){
                    return '
                        <div class="dropdown">
                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ri-more-fill align-middle"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a href="'.route('product.options', $row->id).'" class="dropdown-item">
                                        <i class="ri-list-settings-fill align-bottom me-2 text-muted"></i> Options
                                    </a>
                                </li>
                                <li class="dropdown-divider"></li>
                                <li>
                                    <a href="'.route('product.sort', $row->category_id).'" class="dropdown-item">
                                        <i class="ri-drag-move-fill align-bottom me-2 text-muted"></i> Sort Products
                                    </a>
                                </li>
                                <li class="dropdown-divider"></li>
                                <li>
                                    <button class="dropdown-item" id="EditBtn" rid="'.$row->id.'">
                                        <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit
                                    </button>
                                </li>
                                <li class="dropdown-divider"></li>
                                <li>
                                    <button class="dropdown-item deleteBtn" 
                                        data-delete-url="'.route('product.destroy',$row->id).'" 
                                        data-method="DELETE" 
                                        data-table="#productTable">
                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                    </button>
                                </li>
                            </ul>
                        </div>
                    ';
                })

                ->filterColumn('category_name', function($query, $keyword) {
                    $query->whereHas('category', function($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('price', function($query, $keyword) {
                    $keyword = ltrim($keyword, '£');
                    $query->where('price', 'like', "%{$keyword}%");
                })
                ->filterColumn('sku_ref', function($query, $keyword) {
                    $query->where('sku_ref', 'like', "%{$keyword}%");
                })
                ->rawColumns(['status','action','image','sidebar','stock_status'])
                ->make(true);
        }

        $categories = Category::orderBy('sl', 'asc')->get();
        $tags = Tag::where('status', 1)->get();
        return view('admin.product.index', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'title' => trim($request->title),
        ]);

        $request->validate([
            'title' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'tag_id' => 'nullable|exists:tags,id',
            'price' => 'required|numeric|min:0',
            'sku_ref' => 'required|string',
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'has_attribute' => 'nullable|boolean',
            'attribute_name' => 'nullable|string|max:255',
            'attribute_price' => 'nullable|numeric|min:0'
        ]);

        $exists = Product::where('title', $request->title)
            ->where('category_id', $request->category_id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'This product already exists in this category!'], 422);
        }

        $baseSlug = Str::slug($request->title);
        $slugExists = Product::where('slug', $baseSlug)->exists();
        
        if ($slugExists) {
            $category = Category::findOrFail($request->category_id);
            $slug = Str::slug($category->name . '-' . $request->title);
        } else {
            $slug = $baseSlug;
        }

        $product = new Product();
        $product->title = $request->title;
        $product->slug = $slug;
        $product->category_id = $request->category_id;
        $product->tag_id = $request->tag_id;
        $product->price = $request->price;
        $product->sku_ref = $request->sku_ref;
        $product->short_description = $request->short_description;
        $product->long_description = $request->long_description;
        $product->status = $request->product_type == 'sub' ? 0 : 1;
        
        $product->has_attribute = $request->has_attribute ? 1 : 0;
        if ($request->has_attribute) {
            $product->attribute_name = $request->attribute_name;
            $product->attribute_price = $request->attribute_price ?? 0;
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $name = mt_rand(10000000,99999999).'.webp';
            $path = public_path('uploads/products/');
            if(!file_exists($path)) mkdir($path, 0755, true);

            Image::make($file)
                ->resize(1200, null, fn($c) => $c->aspectRatio())
                ->encode('webp', 50)
                ->save($path.$name);

            $product->image = '/uploads/products/'.$name;
        }

        $product->save();
        return response()->json(['message' => 'Product created successfully!'], 200);
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request)
    {
        $request->merge([
            'title' => trim($request->title),
        ]);

        $request->validate([
            'title' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'tag_id' => 'nullable|exists:tags,id',
            'price' => 'required|numeric|min:0',
            'sku_ref' => 'required|string',
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'has_attribute' => 'nullable|boolean',
            'attribute_name' => 'nullable|string|max:255',
            'attribute_price' => 'nullable|numeric|min:0'
        ]);

        $product = Product::findOrFail($request->codeid);

        $exists = Product::where('title', $request->title)
            ->where('category_id', $request->category_id)
            ->where('id', '!=', $request->codeid)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'This product already exists in this category!'], 422);
        }

        $baseSlug = Str::slug($request->title);
        $slugExists = Product::where('slug', $baseSlug)->where('id', '!=', $request->codeid)->exists();
        
        if ($slugExists) {
            $category = Category::findOrFail($request->category_id);
            $slug = Str::slug($category->name . '-' . $request->title);
        } else {
            $slug = $baseSlug;
        }

        $product->title = $request->title;
        $product->slug = $slug;
        $product->category_id = $request->category_id;
        $product->tag_id = $request->tag_id;
        $product->price = $request->price;
        $product->sku_ref = $request->sku_ref;
        $product->short_description = $request->short_description;
        $product->long_description = $request->long_description;
        $product->status = $request->product_type == 'sub' ? 0 : 1;
        
        $product->has_attribute = $request->has_attribute ? 1 : 0;
        if ($request->has_attribute) {
            $product->attribute_name = $request->attribute_name;
            $product->attribute_price = $request->attribute_price ?? 0;
        } else {
            $product->attribute_name = null;
            $product->attribute_price = 0;
        }

        if ($request->hasFile('image')) {
            if($product->image && $product->image != '/placeholder.webp' && file_exists(public_path($product->image))){
                @unlink(public_path($product->image));
            }

            $file = $request->file('image');
            $name = mt_rand(10000000,99999999).'.webp';
            $path = public_path('uploads/products/');
            if(!file_exists($path)) mkdir($path, 0755, true);

            Image::make($file)
                ->resize(1200, null, fn($c) => $c->aspectRatio())
                ->encode('webp', 50)
                ->save($path.$name);

            $product->image = '/uploads/products/'.$name;
        }

        $product->save();
        return response()->json(['message' => 'Product updated successfully!'], 200);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if($product->image && $product->image != '/placeholder.webp' && file_exists(public_path($product->image))){
            @unlink(public_path($product->image));
        }
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully.'], 200);
    }

    public function toggleStatus(Request $request)
    {
        $product = Product::findOrFail($request->product_id);
        $product->update(['status' => $request->status]);
        return response()->json(['message' => 'Product status updated successfully.'], 200);
    }

    public function toggleSidebar(Request $request)
    {
        $product = Product::findOrFail($request->product_id);
        $product->update(['show_in_menu' => $request->show_in_menu]);
        return response()->json(['message' => 'Visibility updated successfully.'], 200);
    }

    public function toggleStockStatus(Request $request)
    {
        $product = Product::findOrFail($request->product_id);
        $product->update(['stock_status' => $request->stock_status]);
        return response()->json(['message' => 'Stock status updated successfully.'], 200);
    }

    public function removeImage($id)
    {
        $product = Product::findOrFail($id);
        if($product->image && $product->image != '/placeholder.webp' && file_exists(public_path($product->image))){
            @unlink(public_path($product->image));
        }
        $product->update(['image' => '/placeholder.webp']);
        return response()->json(['message' => 'Image removed successfully!'], 200);
    }

    public function sortView($category_id)
    {
        $category = Category::findOrFail($category_id);
        $products = Product::where('category_id', $category_id)
            ->where('status', 1)
            ->orderBy('sl', 'asc')
            ->get();
        return view('admin.product.sort', compact('category', 'products'));
    }

    public function reorder(Request $request)
    {
        foreach ($request->order as $index => $id) {
            Product::where('id', $id)->update(['sl' => $index + 1]);
        }
        return response()->json(['message' => 'Order updated successfully.']);
    }
}