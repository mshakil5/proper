<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tag;
use Illuminate\Support\Str;
use DataTables;

class TagController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $tags = Tag::select(['id','name','slug','status'])->latest();
            return DataTables::of($tags)
                ->addIndexColumn()
                ->addColumn('status', function($row){
                    $checked = $row->status == 1 ? 'checked' : '';
                    return '<div class="form-check form-switch" dir="ltr">
                                <input type="checkbox" class="form-check-input toggle-status" 
                                       id="customSwitchStatus'.$row->id.'" data-id="'.$row->id.'" '.$checked.'>
                                <label class="form-check-label" for="customSwitchStatus'.$row->id.'"></label>
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
                                    <button class="dropdown-item" id="EditBtn" rid="'.$row->id.'">
                                        <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit
                                    </button>
                                </li>
                                <li class="dropdown-divider"></li>
                                <li>
                                    <button class="dropdown-item deleteBtn" 
                                        data-delete-url="'.route('tag.destroy',$row->id).'" 
                                        data-method="DELETE" 
                                        data-table="#tagTable">
                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                    </button>
                                </li>
                            </ul>
                        </div>
                    ';
                })
                ->rawColumns(['status','action'])
                ->make(true);
        }

        return view('admin.tag.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:tags,name'
        ]);

        Tag::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'created_by' => auth()->id()
        ]);

        return response()->json(['message' => 'Tag created successfully!'], 200);
    }

    public function edit($id)
    {
        $tag = Tag::findOrFail($id);
        return response()->json($tag);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:tags,name,'.$request->codeid
        ]);

        $tag = Tag::findOrFail($request->codeid);
        $tag->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'updated_by' => auth()->id()
        ]);

        return response()->json(['message' => 'Tag updated successfully!'], 200);
    }

    public function destroy($id)
    {
        Tag::findOrFail($id)->delete();
        return response()->json(['message' => 'Tag deleted successfully.'], 200);
    }

    public function toggleStatus(Request $request)
    {
        $tag = Tag::findOrFail($request->tag_id);
        $tag->update(['status' => $request->status]);
        return response()->json(['message' => 'Tag status updated successfully.'], 200);
    }
}