<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master;

class MasterController extends Controller
{
    public function index()
    {
        $data = Master::orderBy('id', 'DESC')->get();
        return view('admin.master.index', compact('data'));
    }

    public function store(Request $request)
    {
        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Name \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $videoSize = $video->getSize(); // Size in bytes
            $maxSize = 20 * 1024 * 1024; // 20MB in bytes
            
            if ($videoSize > $maxSize) {
                $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Video size must be less than 20MB.</b></div>";
                return response()->json(['status'=> 303, 'message'=>$message]);
                exit();
            }
            
            $allowedExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv'];
            $videoExtension = $video->getClientOriginalExtension();
            
            if (!in_array(strtolower($videoExtension), $allowedExtensions)) {
                $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Invalid video format. Allowed formats: MP4, AVI, MOV, WMV, FLV, MKV.</b></div>";
                return response()->json(['status'=> 303, 'message'=>$message]);
                exit();
            }
        }
        
        $data = new Master;
        $data->name = $request->name;
        $data->short_title = $request->short_title;
        $data->long_title = $request->long_title;
        $data->short_description = $request->short_description;
        $data->long_description = $request->long_description;
        $data->meta_title = $request->meta_title;
        $data->meta_description = $request->meta_description;
        $data->meta_keywords = $request->meta_keywords;
        $data->created_by =  auth()->id();

        if ($request->hasFile('meta_image')) {
            $image = $request->file('meta_image');
            $imageName = rand(10000000, 99999999) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/meta_image'), $imageName);
            $data->meta_image = $imageName;
        }

        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $videoName = 'video_' . rand(10000000, 99999999) . '.' . $video->getClientOriginalExtension();
            $video->move(public_path('images/meta_video'), $videoName);
            $data->video = $videoName;
        }

        if ($data->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Created successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    public function edit($id)
    {
        return response()->json(Master::find($id));
    }

    public function update(Request $request)
    {
        $data = Master::find($request->codeid);

        if (!$data) {
            $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Record not found!</b></div>";
            return response()->json(['status' => 404, 'message' => $message]);
        }

        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" Name \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $videoSize = $video->getSize();
            $maxSize = 20 * 1024 * 1024; // 20MB
            
            if ($videoSize > $maxSize) {
                $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Video size must be less than 20MB.</b></div>";
                return response()->json(['status'=> 303, 'message'=>$message]);
                exit();
            }
            
            $allowedExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv'];
            $videoExtension = $video->getClientOriginalExtension();
            
            if (!in_array(strtolower($videoExtension), $allowedExtensions)) {
                $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Invalid video format. Allowed formats: MP4, AVI, MOV, WMV, FLV, MKV.</b></div>";
                return response()->json(['status'=> 303, 'message'=>$message]);
                exit();
            }
        }

        if ($request->hasFile('meta_image')) {
            if ($data->meta_image) {
                $oldImagePath = public_path('images/meta_image/' . $data->meta_image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $image = $request->file('meta_image');
            $imageName = rand(10000000, 99999999) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/meta_image'), $imageName);
            $data->meta_image = $imageName;
        }

        if ($request->hasFile('video')) {
            // Delete old video if exists
            if ($data->video) {
                $oldVideoPath = public_path('images/meta_video/' . $data->video);
                if (file_exists($oldVideoPath)) {
                    unlink($oldVideoPath);
                }
            }

            $video = $request->file('video');
            $videoName = 'video_' . rand(10000000, 99999999) . '.' . $video->getClientOriginalExtension();
            $video->move(public_path('images/meta_video'), $videoName);
            $data->video = $videoName;
        }

        $data->name = $request->name;
        $data->short_title = $request->short_title;
        $data->long_title = $request->long_title;
        $data->short_description = $request->short_description;
        $data->long_description = $request->long_description;
        $data->meta_title = $request->meta_title;
        $data->meta_description = $request->meta_description;
        $data->meta_keywords = $request->meta_keywords;
        $data->updated_by = auth()->id();

        if ($data->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Updated successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    public function delete($id)
    {
        $data = Master::find($id);
        if ($data->delete()) {
            return response()->json(['status' => 300, 'message' => 'Data deleted successfully.']);
        } else {
            return response()->json(['status' => 303, 'message' => 'Server Error!']);
        }
    }

}
