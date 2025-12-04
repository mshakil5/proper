<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductTag;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use Illuminate\Validation\Rule;

class ProductTagController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ProductTag::latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', fn($row) => 
                    '<div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input toggle-status" id="status'.$row->id.'" data-id="'.$row->id.'" '.($row->status ? 'checked' : '').'>
                        <label class="custom-control-label" for="status'.$row->id.'"></label>
                    </div>'
                )
                ->addColumn('action', fn($row) => '
                    <button class="btn btn-sm btn-info edit" data-id="'.$row->id.'"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-danger delete" data-id="'.$row->id.'"><i class="fas fa-trash-alt"></i></button>
                ')
                ->rawColumns(['status','action'])
                ->make(true);
        }

        return view('admin.product-tags.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', Rule::unique('product_tags', 'name')]
        ]);

        if ($validator->fails())
            return response()->json(['status'=>422,'errors'=>$validator->errors()],422);

        ProductTag::create([
            'name' => $request->name,
            'status' => 1,
            'created_by' => auth()->id()
        ]);

        return response()->json(['status'=>200,'message'=>'Product tag created successfully']);
    }

    public function edit($id)
    {
        return response()->json(ProductTag::findOrFail($id));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', Rule::unique('product_tags', 'name')->ignore($request->codeid)]
        ]);

        if ($validator->fails())
            return response()->json(['status'=>422,'errors'=>$validator->errors()],422);

        $productTag = ProductTag::findOrFail($request->codeid);
        $productTag->update([
            'name' => $request->name,
            'updated_by' => auth()->id()
        ]);

        return response()->json(['status'=>200,'message'=>'Product tag updated successfully']);
    }

    public function destroy($id)
    {
        $productTag = ProductTag::find($id);
        if (!$productTag) return response()->json(['success'=>false,'message'=>'Product tag not found'],404);
        $productTag->delete();
        return response()->json(['success'=>true,'message'=>'Product tag deleted successfully']);
    }

    public function toggleStatus(Request $request)
    {
        $productTag = ProductTag::find($request->tag_id);
        if (!$productTag) return response()->json(['status'=>404,'message'=>'Product tag not found']);
        $productTag->status = $request->status;
        $productTag->save();
        return response()->json(['status'=>200,'message'=>'Status updated']);
    }
}