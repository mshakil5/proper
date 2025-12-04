<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GalleryCategory;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class GalleryCategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = GalleryCategory::latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    $checked = $row->status == 1 ? 'checked' : '';
                    return '<div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input toggle-status" id="customSwitchStatus' . $row->id . '" data-id="' . $row->id . '" ' . $checked . '>
                                <label class="custom-control-label" for="customSwitchStatus' . $row->id . '"></label>
                            </div>';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-sm btn-info edit" data-id="'.$row->id.'">Edit</button>
                        <button class="btn btn-sm btn-danger delete" data-id="'.$row->id.'">Delete</button>
                    ';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('admin.gallery_category.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:gallery_categories,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['status'=>422, 'errors'=>$validator->errors()], 422);
        }

        $category = GalleryCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'created_by' => auth()->id(),
        ]);

        return response()->json(['status'=>200, 'message'=>'Category created successfully.', 'category'=>$category]);
    }

    public function edit($id)
    {
        return response()->json(GalleryCategory::findOrFail($id));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:gallery_categories,id',
            'name' => 'required|string|max:255|unique:gallery_categories,name,'.$request->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['status'=>422, 'errors'=>$validator->errors()], 422);
        }

        $category = GalleryCategory::findOrFail($request->id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->updated_by = auth()->id();
        $category->save();

        return response()->json(['status'=>200, 'message'=>'Category updated successfully.', 'category'=>$category]);
    }

    public function destroy($id)
    {
        $category = GalleryCategory::findOrFail($id);
        $category->delete();

        return response()->json(['status'=>200, 'message'=>'Category deleted successfully.']);
    }

    public function toggleStatus(Request $request)
    {
        $category = GalleryCategory::findOrFail($request->id);
        $category->status = $request->status;
        $category->save();

        return response()->json(['status'=>200, 'message'=>'Status updated successfully.']);
    }
}