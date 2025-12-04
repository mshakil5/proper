<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\GalleryCategory;
use App\Models\GalleryImage;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    public function getGallery(Request $request)
    {
        if ($request->ajax()) {
            $data = Gallery::with('category')->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('feature_image', function ($row) {
                    if ($row->feature_image) {
                        return '<img src="' . asset("images/gallery/" . $row->feature_image) . '" style="max-width:100px; height:auto;">';
                    }
                    return '';
                })
                ->addColumn('title', fn($row) => $row->title ?? '')
                ->addColumn('category_name', fn($row) => $row->category->name ?? '-')
                ->addColumn('status', function ($row) {
                    $checked = $row->status ? 'checked' : '';
                    return '<div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input toggle-status" id="customSwitchStatus' . $row->id . '" data-id="' . $row->id . '" ' . $checked . '>
                                <label class="custom-control-label" for="customSwitchStatus' . $row->id . '"></label>
                            </div>';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-sm btn-info edit" data-id="' . $row->id . '">Edit</button>
                        <button class="btn btn-sm btn-danger delete" data-id="' . $row->id . '">Delete</button>
                    ';
                })
                ->rawColumns(['feature_image', 'status', 'action'])
                ->make(true);
        }

        $categories = GalleryCategory::where('status', 1)->get();
        return view('admin.gallery.index', compact('categories'));
    }

    public function galleryStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id'   => 'nullable|exists:gallery_categories,id',
            'title'         => 'nullable|string|max:255',
            'feature_image' => 'required|image|mimes:jpeg,png,webp|max:2048',
            'images.*'      => 'nullable|image|mimes:jpeg,png,webp|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'errors' => $validator->errors()], 422);
        }

        $gallery = new Gallery();
        $gallery->category_id = $request->category_id;
        $gallery->title = $request->title;
        $gallery->description = $request->description;
        $gallery->slug = Str::slug($request->title) . '-' . mt_rand(1000, 9999);
        $gallery->created_by = auth()->id();

        if ($request->hasFile('feature_image')) {
            $file = $request->file('feature_image');
            $filename = mt_rand(100000, 999999) . '.webp';
            $path = public_path('images/gallery/');
            if (!file_exists($path)) mkdir($path, 0755, true);

            Image::make($file)
                ->resize(1200, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 70)
                ->save($path . $filename);

            $gallery->feature_image = $filename;
        }

        $gallery->save();

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $multiFile) {
                $filename = mt_rand(100000, 999999) . '.webp';
                $path = public_path('images/gallery/');

                Image::make($multiFile)
                    ->resize(1200, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->encode('webp', 70)
                    ->save($path . $filename);

                GalleryImage::create([
                    'gallery_id' => $gallery->id,
                    'image'      => $filename
                ]);
            }
        }

        return response()->json(['status' => 200, 'message' => 'Gallery created successfully.']);
    }

    public function galleryEdit($id)
    {
        $gallery = Gallery::with('images')->findOrFail($id);
        return response()->json($gallery);
    }

    public function galleryUpdate(Request $request)
    {
        $gallery = Gallery::with('images')->findOrFail($request->id);

        $gallery->category_id = $request->category_id;
        $gallery->title = $request->title;
        $gallery->description = $request->description;
        $gallery->updated_by = auth()->id();

        $path = public_path('images/gallery/');

        // Feature Image
        if ($request->hasFile('feature_image')) {
            if ($gallery->feature_image && file_exists($path.$gallery->feature_image)) unlink($path.$gallery->feature_image);
            $file = $request->file('feature_image');
            $filename = mt_rand(100000,999999).'.webp';
            Image::make($file)
                  ->resize(1200, null, function ($constraint) {
                      $constraint->aspectRatio();
                      $constraint->upsize();
                  })
                ->encode('webp',70)
                ->save($path.$filename);
            $gallery->feature_image = $filename;
        }

        $gallery->save();

        // Remove old gallery images
        foreach ($gallery->images as $img) {
            if(file_exists($path.$img->image)) unlink($path.$img->image);
        }
        $gallery->images()->delete();

        // Save new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $multiFile) {
                $filename = mt_rand(100000,999999).'.webp';
                Image::make($multiFile)
                    ->resize(1200, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->encode('webp',70)
                    ->save($path.$filename);

                GalleryImage::create([
                    'gallery_id'=>$gallery->id,
                    'image'=>$filename
                ]);
            }
        }

        return response()->json(['status'=>200,'message'=>'Gallery updated successfully.']);
    }

    public function galleryDelete($id)
    {
        $gallery = Gallery::with('images')->findOrFail($id);

        if ($gallery->feature_image && file_exists(public_path('images/gallery/' . $gallery->feature_image))) {
            unlink(public_path('images/gallery/' . $gallery->feature_image));
        }

        foreach ($gallery->images as $img) {
            if (file_exists(public_path('images/gallery/' . $img->image))) {
                unlink(public_path('images/gallery/' . $img->image));
            }
        }

        $gallery->delete();

        return response()->json(['status' => 200, 'message' => 'Gallery deleted successfully.']);
    }

    public function toggleStatus(Request $request)
    {
        $gallery = Gallery::find($request->id);
        if (!$gallery) {
            return response()->json(['status' => 404, 'message' => 'Not found']);
        }
        $gallery->status = $request->status;
        $gallery->save();

        return response()->json(['status' => 200, 'message' => 'Status updated successfully']);
    }
}