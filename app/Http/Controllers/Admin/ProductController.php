<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use Illuminate\Validation\Rule;
use App\Models\Category;
use App\Models\ProductTag;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Product::latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('thumbnail', function($row) {
                    if ($row->image) {
                        return '<a href="'.asset("images/products/".$row->image).'" target="_blank">
                                    <img src="'.asset("images/products/".$row->image).'" style="max-width:80px; height:auto;">
                                </a>';
                    }
                    return '';
                })
                ->addColumn('sl', function ($row) {
                    return $row->sl ?? '';
                })
                ->addColumn('price', function($row) {
                    return $row->price ? '£' . number_format($row->price, 2) : '-';
                })
                ->addColumn('category', function($row) {
                    return $row->category ? $row->category->name : '-';
                })
                ->addColumn('status', function($row) {
                    $checked = $row->status == 1 ? 'checked' : '';
                    return '<div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input toggle-status" id="customSwitchStatus'.$row->id.'" data-id="'.$row->id.'" '.$checked.'>
                                <label class="custom-control-label" for="customSwitchStatus'.$row->id.'"></label>
                            </div>';
                })
                ->addColumn('action', function($row) {
                    return '
                    <div class="dropdown">
                      <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="actionMenu'.$row->id.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      </button>
                      <div class="dropdown-menu p-2" aria-labelledby="actionMenu'.$row->id.'" style="min-width: 160px;">
                        <a class="btn btn-success btn-sm btn-block mb-1 d-none" href="'.route('products.features.index', $row->id).'">Features</a>
                        <a class="btn btn-info btn-sm btn-block mb-1 d-none" href="'.route('products.clients.index', $row->id).'">Sliders</a>
                        <button class="btn btn-primary btn-sm btn-block mb-1 steps d-none" data-id="'.$row->id.'">Steps</button>
                        <a class="btn btn-warning btn-sm btn-block mb-1 d-none" href="'.route('products.faqs.index', $row->id).'">FAQs</a>
                        <hr class="dropdown-divider d-none">
                        <button class="btn btn-outline-primary btn-sm btn-block mb-1 edit" data-id="'.$row->id.'">Edit</button>
                        <button class="btn btn-outline-danger btn-sm btn-block delete" data-id="'.$row->id.'">Delete</button>
                      </div>
                    </div>';
                })
                ->rawColumns(['thumbnail', 'status', 'action', 'price', 'category'])
                ->make(true);
        }
    
        $categories = Category::where('status', 1)->get();
        $tags = ProductTag::where('status', 1)->get();
        return view('admin.products.index', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255', Rule::unique('products')->whereNull('deleted_at')],
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'video' => 'nullable|file|mimetypes:video/mp4,video/webm|max:51200', // 50MB
            'meta_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = new Product;
        $data->title = $request->title;
        $data->slug = Str::slug($request->title);
        $data->sub_title = $request->sub_title;
        $data->url = $request->url;
        $data->short_description = $request->short_description;
        $data->feature_description = $request->feature_description;
        $data->long_description = $request->long_description;
        $data->icon = $request->icon;
        $data->sl = $request->sl ?? 0;
        $data->meta_title = $request->meta_title;
        $data->meta_description = $request->meta_description;
        $data->meta_keywords = $request->meta_keywords;
        $data->created_by = auth()->id();
        $data->category_id = $request->category_id;
        $data->product_tag_id = $request->product_tag_id;
        $data->price = $request->price;

        // Handle main image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.webp';
            $path = public_path('images/products/');

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            // Ensure directory exists
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            // Save main image
            Image::make($image)
                ->resize(1200, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 85)
                ->save($path . $imageName);

            $data->image = $imageName;
        }

        // Handle banner image upload
        if ($request->hasFile('banner_image')) {
            $bannerImage = $request->file('banner_image');
            $bannerImageName = 'banner_' . time() . '.webp';
            $path = public_path('images/products/banners/');

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            Image::make($bannerImage)
                ->resize(1920, 600, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 85)
                ->save($path . $bannerImageName);

            $data->banner_image = $bannerImageName;
        }

        // Handle video upload
        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $videoName = 'video_' . time() . '.' . $video->getClientOriginalExtension();
            $path = public_path('images/products/videos/');

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            $video->move($path, $videoName);
            $data->video = $videoName;
        }

        // Handle meta image upload
        if ($request->hasFile('meta_image')) {
            $metaImage = $request->file('meta_image');
            $metaImageName = 'meta_' . time() . '.webp';
            $path = public_path('images/products/meta/');

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            Image::make($metaImage)
                ->resize(1200, 630, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 80)
                ->save($path . $metaImageName);

            $data->meta_image = $metaImageName;
        }

        if ($data->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Product created successfully.'
            ], 201);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Server error.'
            ], 500);
        }
    }

    public function edit($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'status' => 404,
                'message' => 'Product not found'
            ], 404);
        }
        return response()->json($product);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|' . Rule::unique('products', 'title')->ignore($request->codeid)->whereNull('deleted_at'),
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'video' => 'nullable|file|mimetypes:video/mp4,video/webm|max:51200',
            'meta_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $product = Product::find($request->codeid);
        if (!$product) {
            return response()->json([
                'status' => 404,
                'message' => 'Product not found'
            ], 404);
        }

        $product->title = $request->title;
        $product->slug = Str::slug($request->title);
        $product->sub_title = $request->sub_title;
        $product->url = $request->url;
        $product->short_description = $request->short_description;
        $product->feature_description = $request->feature_description;
        $product->long_description = $request->long_description;
        $product->sl = $request->sl ?? 0;
        $product->icon = $request->icon;
        $product->meta_title = $request->meta_title;
        $product->meta_description = $request->meta_description;
        $product->meta_keywords = $request->meta_keywords;
        $product->updated_by = auth()->id();
        $product->category_id = $request->category_id;
        $product->product_tag_id = $request->product_tag_id;
        $product->price = $request->price;

        // Handle main image update
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image && file_exists(public_path('images/products/' . $product->image))) {
                unlink(public_path('images/products/' . $product->image));
            }

            $image = $request->file('image');
            $imageName = time() . '.webp';
            $path = public_path('images/products/');

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            // Save main image
            Image::make($image)
                ->resize(1200, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 85)
                ->save($path . $imageName);

            $product->image = $imageName;
        }

        // Handle banner image update
        if ($request->hasFile('banner_image')) {
            // Delete old banner image if exists
            if ($product->banner_image && file_exists(public_path('images/products/banners/' . $product->banner_image))) {
                unlink(public_path('images/products/banners/' . $product->banner_image));
            }

            $bannerImage = $request->file('banner_image');
            $bannerImageName = 'banner_' . time() . '.webp';
            $path = public_path('images/products/banners/');
            
            Image::make($bannerImage)
                ->resize(1920, 600, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 85)
                ->save($path . $bannerImageName);

            $product->banner_image = $bannerImageName;
        }

        // Handle video update
        if ($request->hasFile('video')) {
            // Delete old video if exists
            if ($product->video && file_exists(public_path('images/products/videos/' . $product->video))) {
                unlink(public_path('images/products/videos/' . $product->video));
            }

            $video = $request->file('video');
            $videoName = 'video_' . time() . '.' . $video->getClientOriginalExtension();
            $path = public_path('images/products/videos/');
            
            $video->move($path, $videoName);
            $product->video = $videoName;
        }

        // Handle meta image update
        if ($request->hasFile('meta_image')) {
            // Delete old meta image if exists
            if ($product->meta_image && file_exists(public_path('images/products/meta/' . $product->meta_image))) {
                unlink(public_path('images/products/meta/' . $product->meta_image));
            }

            $metaImage = $request->file('meta_image');
            $metaImageName = 'meta_' . time() . '.webp';
            $path = public_path('images/products/meta/');
            
            Image::make($metaImage)
                ->resize(1200, 630, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 80)
                ->save($path . $metaImageName);

            $product->meta_image = $metaImageName;
        }

        if ($product->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Product updated successfully.'
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Server error.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        $product = Product::with(['features', 'clients', 'faqs'])->find($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
        }

        // Delete main product images/videos
        if ($product->image && file_exists(public_path('images/products/' . $product->image))) {
            unlink(public_path('images/products/' . $product->image));
        }

        if ($product->banner_image && file_exists(public_path('images/products/banners/' . $product->banner_image))) {
            unlink(public_path('images/products/banners/' . $product->banner_image));
        }

        if ($product->video && file_exists(public_path('images/products/videos/' . $product->video))) {
            unlink(public_path('images/products/videos/' . $product->video));
        }

        if ($product->meta_image && file_exists(public_path('images/products/meta/' . $product->meta_image))) {
            unlink(public_path('images/products/meta/' . $product->meta_image));
        }

        // Delete Product Features files
        foreach ($product->features as $feature) {
            $path = public_path('images/product-features/' . $feature->image);
            if ($feature->image && file_exists($path)) {
                unlink($path);
            }
            $feature->delete();
        }

        // Delete Product Clients files
        foreach ($product->clients as $client) {
            $path = public_path('images/product-clients/' . $client->image);
            if ($client->image && file_exists($path)) {
                unlink($path);
            }
            $client->delete();
        }


        // Delete FAQs (no files, just remove)
        foreach ($product->faqs as $faq) {
            $faq->delete();
        }

        // Soft delete product
        if ($product->delete()) {
            return response()->json(['success' => true, 'message' => 'Product and related data deleted.']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to delete product.'], 500);
    }

    public function toggleStatus(Request $request)
    {
        $product = Product::find($request->product_id);
        if (!$product) {
            return response()->json(['status' => 404, 'message' => 'Product not found']);
        }

        $product->status = $request->status;
        $product->save();

        return response()->json(['status' => 200, 'message' => 'Status updated successfully']);
    }

    public function getProcess(Product $product)
    {
        $process = $product->process()->firstOrCreate([
            'product_id' => $product->id,
        ]);

        return response()->json([
            'title' => $process->title,
            'sub_title' => $process->sub_title,
            'steps' => $process->steps ? json_decode($process->steps, true) : [],
        ]);
    }
    public function saveProcess(Request $request, Product $product)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'sub_title' => 'nullable|string|max:255',
            'steps' => 'nullable|array',
        ]);

        $process = $product->process()->firstOrCreate([
            'product_id' => $product->id,
        ]);

        $process->update([
            'title' => $request->title,
            'sub_title' => $request->sub_title,
            'steps' => $request->steps ? json_encode($request->steps) : null,
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Product process saved successfully',
            'process' => $process
        ]);
    }
}