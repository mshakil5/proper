<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class ContentCategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ContentCategory::latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    $checked = $row->status ? 'checked' : '';
                    return '<div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input toggle-status" id="customSwitchStatus'.$row->id.'" data-id="'.$row->id.'" '.$checked.'>
                                <label class="custom-control-label" for="customSwitchStatus'.$row->id.'"></label>
                            </div>';
                })
                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-sm btn-info edit" data-id="'.$row->id.'"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-danger delete" data-id="'.$row->id.'"><i class="fas fa-trash alt"></i></button>';
                })
                ->rawColumns(['status','action'])
                ->make(true);
        }

        return view('admin.content_category.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:content_categories,name',
        ]);

        $category = new ContentCategory();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->save();

        return response()->json(['status'=>200,'message'=>'Category created successfully']);
    }

    public function edit($id)
    {
        $category = ContentCategory::findOrFail($id);
        return response()->json($category);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:content_categories,id',
            'name' => 'required|string|max:255|unique:content_categories,name,'.$request->id,
        ]);

        $category = ContentCategory::findOrFail($request->id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->save();

        return response()->json(['status'=>200,'message'=>'Category updated successfully']);
    }

    public function destroy($id)
    {
        $category = ContentCategory::findOrFail($id);
        $category->delete();

        return response()->json(['status'=>200,'message'=>'Category deleted successfully']);
    }

    public function toggleStatus(Request $request)
    {
        $category = ContentCategory::findOrFail($request->id);
        $category->status = $request->status;
        $category->save();

        return response()->json(['status'=>200,'message'=>'Status updated successfully']);
    }
}
