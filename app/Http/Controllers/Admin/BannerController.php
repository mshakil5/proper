<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class BannerController extends Controller
{
    
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Banner::latest();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('feature_image', function ($row) {
                        $path = asset('images/banner/' . $row->feature_image);
                        return '<img src="' . $path . '" alt="Image" class="img-thumbnail feature-img" 
                                    style="width: 200px; height: 80px; cursor:pointer;" 
                                    data-full="' . $path . '">';
                    })
                    ->addColumn('status', function ($row) {
                        $checked = $row->status ? 'checked' : '';
                        return '<div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input toggle-status" id="customSwitchStatus'.$row->id.'" data-id="'.$row->id.'" '.$checked.'>
                                    <label class="custom-control-label" for="customSwitchStatus'.$row->id.'"></label>
                                </div>';
                    })
                    ->addColumn('action', function ($row) {
                        return '<button class="btn btn-sm btn-info edit" data-id="'.$row->id.'"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-danger delete" data-id="'.$row->id.'"><i class="fas fa-trash-alt"></i></button>';
                    })
                    ->rawColumns(['feature_image','status','action'])
                    ->make(true);

        }

        return view('admin.banner.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'page' => 'required|string|max:255',
        ]);

        $data = new Banner();
        $data->page = $request->page;   

        if ($request->hasFile('image')) {
            $uploadedFile = $request->file('image');
            $imageName = mt_rand(10000000, 99999999) . '.webp';
            $destinationPath = public_path('images/banner/');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            Image::make($uploadedFile)
                ->fit(1920, 600, function ($constraint) {
                    $constraint->upsize();
                }, 'center')
                ->encode('webp', 80)
                ->save($destinationPath . $imageName);

            $data->feature_image = $imageName;
        }
        $data->save();

        return response()->json(['status'=>200,'message'=>'Data created successfully']);
    }

    public function edit($id)
    {
        $category = Banner::findOrFail($id);
        return response()->json($category);
    }

    public function update(Request $request)
    {
        $request->validate([
            'page' => 'required|string|max:255',
        ]);

        $data = Banner::findOrFail($request->id);
        $data->page = $request->page;   

        
        if ($request->hasFile('image')) {
            $uploadedFile = $request->file('image');
            $imageName = mt_rand(10000000, 99999999) . '.webp';
            $destinationPath = public_path('images/banner/');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            Image::make($uploadedFile)
                ->fit(1920, 600, function ($constraint) {
                    $constraint->upsize();
                }, 'center')
                ->encode('webp', 80)
                ->save($destinationPath . $imageName);

            $data->feature_image = $imageName;
        }
        $data->save();

        return response()->json(['status'=>200,'message'=>'Data updated successfully']);
    }

    public function destroy($id)
    {
        $category = Banner::findOrFail($id);
        $category->delete();

        return response()->json(['status'=>200,'message'=>'Data deleted successfully']);
    }

    public function toggleStatus(Request $request)
    {
        $category = Banner::findOrFail($request->id);
        $category->status = $request->status;
        $category->save();

        return response()->json(['status'=>200,'message'=>'Status updated successfully']);
    }
}
