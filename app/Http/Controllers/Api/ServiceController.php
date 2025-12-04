<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Cache::remember('api_active_services', 3600, function () {
            return Service::where('status', 1)->where('type', 1)->latest()->get();
        });

        return response()->json([
            'status' => 200,
            'count' => $services->count(),
            'data' => $services
        ]);
    }

    public function show($id)
    {
        $service = Service::where('id', $id)
            ->orWhere('slug', $id)
            ->first();

        if (!$service) {
            return response()->json(['status' => 404, 'message' => 'Service not found.'], 404);
        }

        return response()->json(['status' => 200, 'data' => $service]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:services,title',
            'amount' => 'required|numeric|min:0',
            'short_desc' => 'nullable|string',
            'long_desc' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'youtube_link' => 'nullable|url|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:webp,jpg,png,jpeg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'errors' => $validator->errors()], 422);
        }

        $service = new Service();
        $service->fill($request->only([
            'title', 'short_desc', 'long_desc', 'amount', 'icon',
            'youtube_link', 'meta_title', 'meta_description', 'meta_keywords'
        ]));
        $service->slug = Str::slug($request->title);
        $service->type = 1;
        $service->created_by = auth('api')->id();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name = uniqid() . '.webp';
            $path = public_path('images/service/');
            if (!file_exists($path)) mkdir($path, 0755, true);

            Image::make($image)->resize(800, null, function ($c) {
                $c->aspectRatio();
            })->encode('webp', 80)->save($path . $name);

            $service->image = $name;
        }

        $service->save();

        return response()->json(['status' => 201, 'message' => 'Service created successfully', 'data' => $service]);
    }

    public function update(Request $request, $id)
    {
        $service = Service::find($id);
        if (!$service) {
            return response()->json(['status' => 404, 'message' => 'Service not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => ['required','string','max:255', Rule::unique('services')->ignore($id)],
            'amount' => 'required|numeric|min:0',
            'short_desc' => 'nullable|string',
            'long_desc' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'youtube_link' => 'nullable|url|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:webp,jpg,png,jpeg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'errors' => $validator->errors()], 422);
        }

        $service->fill($request->only([
            'title', 'short_desc', 'long_desc', 'amount', 'icon',
            'youtube_link', 'meta_title', 'meta_description', 'meta_keywords', 'status'
        ]));
        $service->slug = Str::slug($request->title);
        $service->updated_by = auth('api')->id();

        if ($request->hasFile('image')) {
            if ($service->image && file_exists(public_path('images/service/' . $service->image))) {
                unlink(public_path('images/service/' . $service->image));
            }
            $image = $request->file('image');
            $name = uniqid() . '.webp';
            $path = public_path('images/service/');
            Image::make($image)->resize(800, null, function ($c) {
                $c->aspectRatio();
            })->encode('webp', 80)->save($path . $name);
            $service->image = $name;
        }

        $service->save();
        Cache::forget('api_active_services');

        return response()->json(['status' => 200, 'message' => 'Service updated successfully', 'data' => $service]);
    }

    public function destroy($id)
    {
        $service = Service::find($id);
        if (!$service) {
            return response()->json(['status' => 404, 'message' => 'Service not found'], 404);
        }

        if ($service->image && file_exists(public_path('images/service/' . $service->image))) {
            unlink(public_path('images/service/' . $service->image));
        }

        $service->delete();
        Cache::forget('api_active_services');

        return response()->json(['status' => 200, 'message' => 'Service deleted successfully']);
    }
}