<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master;
use Yajra\DataTables\Facades\DataTables;

class MasterController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Master::orderByDesc('id');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    return '
                        <div class="dropdown">
                            <button class="btn btn-soft-secondary btn-sm" data-bs-toggle="dropdown"><i class="ri-more-fill"></i></button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><button class="dropdown-item EditBtn" data-id="'.$row->id.'"><i class="ri-pencil-fill me-2"></i>Edit</button></li>
                                <li class="dropdown-divider"></li>
                                <li><button class="dropdown-item deleteBtn" data-delete-url="'.route('master.delete', $row->id).'"><i class="ri-delete-bin-fill me-2"></i>Delete</button></li>
                            </ul>
                        </div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.master.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:masters,name,' . $request->id,
        ]);

        $data = new Master();
        $data->fill($request->except('meta_image'));
        $data->created_by = auth()->id();

        if ($request->hasFile('meta_image')) {
            $image = $request->file('meta_image');
            $imageName = uniqid().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('uploads/meta_image'), $imageName);
            $data->meta_image = $imageName;
        }

        $data->save();

        return response()->json(['message' => 'Master data created successfully.'], 201);
    }

    public function edit($id)
    {
        $data = Master::findOrFail($id);
        return response()->json($data);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'   => 'required|exists:masters,id',
            'name' => 'required|string|max:255|unique:masters,name,' . $request->id,
        ]);

        $data = Master::findOrFail($request->id);
        $data->fill($request->except('meta_image'));
        $data->updated_by = auth()->id();

        if ($request->hasFile('meta_image')) {
            if ($data->meta_image && file_exists(public_path('uploads/meta_image/'.$data->meta_image))) {
                unlink(public_path('uploads/meta_image/'.$data->meta_image));
            }
            $image = $request->file('meta_image');
            $imageName = uniqid().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('uploads/meta_image'), $imageName);
            $data->meta_image = $imageName;
        }

        $data->save();

        return response()->json(['message' => 'Master data updated successfully.'], 200);
    }

    public function destroy($id)
    {
        $data = Master::findOrFail($id);
        if ($data->meta_image && file_exists(public_path('uploads/meta_image/'.$data->meta_image))) {
            unlink(public_path('uploads/meta_image/'.$data->meta_image));
        }
        $data->delete();
        return response()->json(['message' => 'Master data deleted successfully.'], 200);
    }
}