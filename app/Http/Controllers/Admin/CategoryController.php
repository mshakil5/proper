<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Category::latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('sl', function($row) {
                    return $row->sl;
                })
                ->addColumn('status', fn($row) => 
                    '<div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input toggle-status" id="status'.$row->id.'" data-id="'.$row->id.'" '.($row->status ? 'checked' : '').'>
                        <label class="custom-control-label" for="status'.$row->id.'"></label>
                    </div>'
                )
                ->addColumn('action', fn($row) => '
                    <a href="'.route('categories.sort').'" class="btn btn-sm btn-warning sort-btn" title="Sort Categories">
                        <i class="fas fa-sort"></i>
                    </a>
                    <button class="btn btn-sm btn-info edit" data-id="'.$row->id.'"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-danger delete" data-id="'.$row->id.'"><i class="fas fa-trash-alt"></i></button>
                ')
                ->rawColumns(['status','action'])
                ->make(true);
        }

        return view('admin.categories.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')]
        ]);

        if ($validator->fails())
            return response()->json(['status'=>422,'errors'=>$validator->errors()],422);

        Category::create([
            'name' => $request->name,
            'status' => 1,
        ]);

        return response()->json(['status'=>200,'message'=>'Category created successfully']);
    }

    public function edit($id)
    {
        return response()->json(Category::findOrFail($id));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($request->codeid)]
        ]);

        if ($validator->fails())
            return response()->json(['status'=>422,'errors'=>$validator->errors()],422);

        $category = Category::findOrFail($request->codeid);
        $category->update([
            'name' => $request->name
        ]);

        return response()->json(['status'=>200,'message'=>'Category updated successfully']);
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) return response()->json(['success'=>false,'message'=>'Category not found'],404);
        $category->delete();
        return response()->json(['success'=>true,'message'=>'Category deleted successfully']);
    }

    public function toggleStatus(Request $request)
    {
        $category = Category::find($request->category_id);
        if (!$category) return response()->json(['status'=>404,'message'=>'Category not found']);
        $category->status = $request->status;
        $category->save();
        return response()->json(['status'=>200,'message'=>'Status updated']);
    }

    public function sortCategories()
    {
        $categories = Category::orderBy('sl')->get();
        return view('admin.categories.sort', compact('categories'));
    }

    public function updateCategoryOrder(Request $request)
    {
        $order = $request->order;
        foreach ($order as $index => $id) {
            Category::where('id', $id)->update(['sl' => $index + 1]);
        }

        return response()->json(['status' => 'success', 'message' => 'Category order updated successfully']);
    }
}