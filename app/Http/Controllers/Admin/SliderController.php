<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slider;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Cache;

class SliderController extends Controller
{
    public function getSlider(Request $request)
    {
        if ($request->ajax()) {
            $sliders = Slider::select(['id','title','image','serial','status'])->orderBy('serial');
            return DataTables::of($sliders)
                ->addIndexColumn()
                ->addColumn('image', function($row){
                    return $row->image 
                        ? '<img src="'.asset($row->image).'" class="img-thumbnail" style="width:50px;height:50px;">'
                        : '';
                })
                ->addColumn('status', function($row){
                    $checked = $row->status == 1 ? 'checked' : '';
                    return '<div class="form-check form-switch" dir="ltr">
                                <input type="checkbox" class="form-check-input toggle-status" 
                                       id="customSwitchStatus'.$row->id.'" data-id="'.$row->id.'" '.$checked.'>
                                <label class="form-check-label" for="customSwitchStatus'.$row->id.'"></label>
                            </div>';
                })
                ->addColumn('serial', function($row){
                    return '<span class="serial-text">'.$row->serial.'</span>';
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
                                        data-delete-url="'.route('slider.delete',$row->id).'" 
                                        data-method="DELETE" 
                                        data-table="#sliderTable">
                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                    </button>
                                </li>
                            </ul>
                        </div>
                    ';
                })
                ->rawColumns(['image','status','serial','action'])
                ->make(true);
        }

        $sliders = Slider::orderBy('serial')->get();
        return view('admin.slider.index', compact('sliders'));
    }

    public function sliderStore(Request $request)
    {
        $request->validate([
            'title'=>'nullable|unique:sliders,title',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120'
        ]);

        $data = new Slider();
        $data->title = $request->title;
        $data->link = $request->link;
        $data->created_by = auth()->id();

        $lastSerial = Slider::max('serial');
        $data->serial = $lastSerial ? $lastSerial + 1 : 1;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $name = mt_rand(10000000,99999999).'.webp';
            $path = public_path('uploads/sliders/');
            if(!file_exists($path)) mkdir($path,0755,true);

            Image::make($file)
                ->resize(1200,null,fn($c)=>$c->aspectRatio())
                ->encode('webp',50)
                ->save($path.$name);

            $data->image = '/uploads/sliders/'.$name;
        } else {
            $data->image = '/placeholder.webp';
        }

        $data->save();
        return response()->json(['message'=>'Slider created successfully!'],200);
    }

    public function sliderEdit($id)
    {
        $slider = Slider::findOrFail($id);
        return response()->json($slider);
    }

    public function sliderUpdate(Request $request)
    {
        $request->validate([
            'title'=>'nullable|unique:sliders,title,'.$request->codeid,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120'
        ]);

        $slider = Slider::findOrFail($request->codeid);
        $slider->title = $request->title;
        $slider->link = $request->link;
        $slider->updated_by = auth()->id();

        if ($request->hasFile('image')) {
            if($slider->image && $slider->image != '/placeholder.webp' && file_exists(public_path($slider->image))){
                @unlink(public_path($slider->image));
            }
            $file = $request->file('image');
            $name = mt_rand(10000000,99999999).'.webp';
            $path = public_path('uploads/sliders/');
            if(!file_exists($path)) mkdir($path,0755,true);

            Image::make($file)
                ->resize(1200,null,fn($c)=>$c->aspectRatio())
                ->encode('webp',50)
                ->save($path.$name);

            $slider->image = '/uploads/sliders/'.$name;
        }

        $slider->save();
        return response()->json(['message'=>'Slider updated successfully!'],200);
    }

    public function sliderDelete($id)
    {
        $slider = Slider::findOrFail($id);
        if($slider->image && $slider->image != '/placeholder.webp' && file_exists(public_path($slider->image))){
            @unlink(public_path($slider->image));
        }
        $slider->delete();
        return response()->json(['message'=>'Slider deleted successfully.'],200);
    }

    public function toggleStatus(Request $request)
    {
        $slider = Slider::findOrFail($request->slider_id);
        $slider->status = $request->status;
        $slider->save();
        return response()->json(['message'=>'Slider status updated successfully.'],200);
    }

    public function updateOrder(Request $request)
    {
        $order = $request->order;
        foreach($order as $index=>$id){
            Slider::where('id',$id)->update(['serial'=>$index+1]);
        }
        return response()->json(['success'=>true,'message'=>'Slider order updated successfully!']);
    }

    public function removeImage($id)
    {
        $slider = Slider::findOrFail($id);
        if($slider->image && $slider->image != '/placeholder.webp' && file_exists(public_path($slider->image))){
            @unlink(public_path($slider->image));
        }
        $slider->update(['image' => '/placeholder.webp']);
        return response()->json(['message' => 'Image removed successfully!'], 200);
    }
}