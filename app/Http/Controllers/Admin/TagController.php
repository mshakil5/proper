<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tag;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use Str;

class TagController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Tag::latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function($row) {
                    $checked = $row->status == 1 ? 'checked' : '';
                    return '<div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input toggle-status" 
                                       id="customSwitch'.$row->id.'" 
                                       data-id="'.$row->id.'" '.$checked.'>
                                <label class="custom-control-label" for="customSwitch'.$row->id.'"></label>
                            </div>';
                })
                ->addColumn('action', function($row) {
                    return '
                      <button class="btn btn-sm btn-info edit" data-id="'.$row->id.'"><i class="fas fa-edit"></i></button>
                      <button class="btn btn-sm btn-danger delete" data-id="'.$row->id.'"><i class="fas fa-trash-alt"></i></button>
                    ';
                })
                ->rawColumns(['status','action'])
                ->make(true);
        }
        return view('admin.tags.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:tags,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['status'=>422,'errors'=>$validator->errors()],422);
        }

        Tag::create([
            'name'=>$request->name,
            'slug'=>Str::slug($request->name),
            'status'=>1,
            'created_by'=>auth()->id()
        ]);

        return response()->json(['status'=>200,'message'=>'Tag created successfully.']);
    }

    public function edit($id)
    {
        $tag = Tag::find($id);
        if(!$tag) return response()->json(['status'=>404,'message'=>'Tag not found'],404);
        return response()->json($tag);
    }

    public function update(Request $request)
    {
        $tag = Tag::find($request->codeid);
        if(!$tag) return response()->json(['status'=>404,'message'=>'Tag not found'],404);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:tags,name,'.$tag->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['status'=>422,'errors'=>$validator->errors()],422);
        }

        $tag->update([
            'name'=>$request->name,
            'slug'=>Str::slug($request->name),
            'updated_by'=>auth()->id()
        ]);

        return response()->json(['status'=>200,'message'=>'Tag updated successfully.']);
    }

    public function destroy($id)
    {
        $tag = Tag::find($id);
        if(!$tag) return response()->json(['success'=>false,'message'=>'Tag not found'],404);

        $tag->delete();
        return response()->json(['success'=>true,'message'=>'Tag deleted successfully.']);
    }

    public function toggleStatus(Request $request)
    {
        $tag = Tag::find($request->id);
        if(!$tag) return response()->json(['status'=>404,'message'=>'Tag not found']);

        $tag->status = $request->status;
        $tag->save();

        return response()->json(['status'=>200,'message'=>'Status updated successfully']);
    }
}
