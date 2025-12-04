<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\ContentCategory;
use App\Models\ContentImage;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use App\Models\Tag;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Cache;

class ContentController extends Controller
{
    public function index(Request $request, $type)
    {
        if ($request->ajax()) {
            $data = Content::with('category')->where('type',$type)->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('feature_image', fn($row) => $row->feature_image ? '<img src="'.asset("images/content/".$row->feature_image).'" style="max-width:100px;">' : '')
                ->addColumn('category_name', fn($row) => $row->category->name ?? '-')
                ->addColumn('status', function ($row) {
                    $checked = $row->status ? 'checked' : '';
                    return '<div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input toggle-status" id="customSwitchStatus' . $row->id . '" data-id="' . $row->id . '" ' . $checked . '>
                                <label class="custom-control-label" for="customSwitchStatus' . $row->id . '"></label>
                            </div>';
                })
                ->addColumn('action', fn($row) => '<button class="btn btn-sm btn-info edit" data-id="'.$row->id.'"><i class="fas fa-edit"></i></button> <button class=" btn btn-sm btn-danger delete" data-id="'.$row->id.'"><i class="fas fa-trash-alt"></i></button>')
                ->rawColumns(['feature_image','status','action'])
                ->make(true);
        }

        $categories = ContentCategory::where('status',1)->get();
        $tags = Tag::where('status',1)->get();
        return view('admin.content.index', compact('categories','type','tags'));
    }

    public function store(Request $request, $type)
    {
        $validator = Validator::make($request->all(), [
            'short_title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('contents')->where(function ($query) use ($type) {
                    return $query->where('type', $type);
                })
            ],
            'long_title' => 'nullable|string|max:255',
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'category_id' => 'required|exists:content_categories,id',
            'feature_image' => 'required|image|mimes:jpeg,png,webp|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id'
        ]);

        if ($validator->fails()){
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $content = new Content();
        $content->category_id = $request->category_id;
        $content->short_title = $request->short_title;
        $content->publishing_date = $request->publishing_date;
        $content->long_title = $request->long_title;
        $content->short_description = $request->short_description;
        $content->long_description = $request->long_description;
        $content->slug = $type . '-' . Str::slug($request->short_title);
        $content->type = $type;
        $content->created_by = auth()->id();
        $content->meta_title = $request->meta_title;
        $content->meta_description = $request->meta_description;
        $content->meta_keywords = $request->meta_keywords;

        if ($request->hasFile('feature_image')){
            $file = $request->file('feature_image');
            $filename = mt_rand(100000,999999).'.webp';
            $path = public_path('images/content/');
            if(!file_exists($path)) mkdir($path,0755,true);

            Image::make($file)
            ->fit(1000, 700)
            ->encode('webp', 70)
            ->save($path . $filename);
            $content->feature_image = $filename;
        }

        if ($request->hasFile('meta_image')) {
            $file = $request->file('meta_image');
            $filename = mt_rand(100000,999999) . '.webp';
            $path = public_path('images/content/');
            Image::make($file)->resize(1200, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode('webp', 70)
            ->save($path.$filename);

            $content->meta_image = $filename;
        }

        $content->save();

        if($request->hasFile('images')){
            foreach($request->file('images') as $multiFile){
                $filename = mt_rand(100000,999999).'.webp';
                Image::make($multiFile)->resize(1200,null,function($c){$c->aspectRatio();$c->upsize();})->encode('webp',70)->save($path.$filename);
                ContentImage::create(['content_id'=>$content->id,'image'=>$filename]);
            }
        }

        if ($request->tags) {
            $content->tags()->sync($request->tags);
        }

        $typeMap = [
            1 => 'active_galleries',
            2 => 'active_blogs',
            3 => 'active_events',
            4 => 'active_news',
        ];

        if(isset($typeMap[$type])){
            Cache::forget($typeMap[$type]);
        }

        return response()->json(['status'=>200,'message'=>'Content created successfully']);
    }

    public function edit($type, $id)
    {
        $content = Content::with(['images', 'tags'])->findOrFail($id);
        return response()->json($content);
    }

    public function update(Request $request, $type)
    {
        $validator = Validator::make($request->all(), [
            'short_title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('contents')->ignore($request->id)->where(function ($query) use ($type) {
                    return $query->where('type', $type);
                })
            ],
            'long_title' => 'nullable|string|max:255',
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'category_id' => 'required|exists:content_categories,id',
            'feature_image' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
            'meta_image' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id'
        ]);

        if ($validator->fails()){
            return response()->json(['status'=>422,'errors'=>$validator->errors()]);
        }

        $content = Content::with('images')->findOrFail($request->id);

        $content->category_id = $request->category_id;
        $content->short_title = $request->short_title;
        $content->long_title = $request->long_title;
        $content->publishing_date = $request->publishing_date;
        $content->short_description = $request->short_description;
        $content->long_description = $request->long_description;
        $content->slug = $type . '-' . Str::slug($request->short_title);
        $content->updated_by = auth()->id();
        $content->meta_title = $request->meta_title;
        $content->meta_description = $request->meta_description;
        $content->meta_keywords = $request->meta_keywords;

        $path = public_path('images/content/');

        // Feature image
        if ($request->hasFile('feature_image')) {
            if ($content->feature_image && file_exists($path . $content->feature_image)) {
                unlink($path . $content->feature_image);
            }
            $file = $request->file('feature_image');
            $filename = mt_rand(100000,999999) . '.webp';
            Image::make($file)
            ->fit(1000, 700)
            ->encode('webp', 70)
            ->save($path . $filename);
            $content->feature_image = $filename;
        }

        // Meta image
        if ($request->hasFile('meta_image')) {
            if ($content->meta_image && file_exists($path . $content->meta_image)) {
                unlink($path . $content->meta_image);
            }
            $file = $request->file('meta_image');
            $filename = mt_rand(100000,999999) . '.webp';
            Image::make($file)->resize(1200,null,function($c){$c->aspectRatio();$c->upsize();})
                ->encode('webp',70)->save($path.$filename);
            $content->meta_image = $filename;
        }

        $content->save();

        // Additional images
        foreach($content->images as $img){
            if(file_exists($path.$img->image)) unlink($path.$img->image);
        }
        $content->images()->delete();

        if($request->hasFile('images')){
            foreach($request->file('images') as $multiFile){
                $filename = mt_rand(100000,999999).'.webp';
                Image::make($multiFile)->resize(1200,null,function($c){$c->aspectRatio();$c->upsize();})
                    ->encode('webp',70)->save($path.$filename);
                ContentImage::create(['content_id'=>$content->id,'image'=>$filename]);
            }
        }

        // Sync tags
        if ($request->tags) {
            $content->tags()->sync($request->tags);
        } else {
            $content->tags()->sync([]);
        }

        $typeMap = [
            1 => 'active_galleries',
            2 => 'active_blogs',
            3 => 'active_events',
            4 => 'active_news',
        ];

        if(isset($typeMap[$type])){
            Cache::forget($typeMap[$type]);
        }

        return response()->json(['status'=>200,'message'=>'Content updated successfully']);
    }

    public function delete($type,$id){
        $content = Content::with('images')->findOrFail($id);
        $path = public_path('images/content/');

        if($content->feature_image && file_exists($path.$content->feature_image)) unlink($path.$content->feature_image);
        if($content->meta_image && file_exists($path.$content->meta_image)) {
            unlink($path.$content->meta_image);
        }

        foreach($content->images as $img){
            if(file_exists($path.$img->image)) unlink($path.$img->image);
        }
        $content->delete();

        $typeMap = [
            1 => 'active_galleries',
            2 => 'active_blogs',
            3 => 'active_events',
            4 => 'active_news',
        ];

        if(isset($typeMap[$type])){
            Cache::forget($typeMap[$type]);
        }
        return response()->json(['status'=>200,'message'=>'Content deleted successfully']);
    }

    public function toggleStatus(Request $request, $type){
        $content = Content::find($request->id);
        $content->status = $request->status;
        $content->save();

        $typeMap = [
            1 => 'active_galleries',
            2 => 'active_blogs',
            3 => 'active_events',
            4 => 'active_news',
        ];

        if(isset($typeMap[$type])){
            Cache::forget($typeMap[$type]);
        }
        return response()->json(['status'=>200,'message'=>'Status updated']);
    }
}
