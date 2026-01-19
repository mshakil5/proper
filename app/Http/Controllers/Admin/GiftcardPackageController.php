<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GiftcardPackage;
use Illuminate\Http\Request;
use DataTables;
use Intervention\Image\Facades\Image;

class GiftcardPackageController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $packages = GiftcardPackage::select(['id', 'name', 'amount', 'image', 'is_active']);
            return DataTables::of($packages)
                ->addIndexColumn()
                ->addColumn('image', function($row){
                    return $row->image
                        ? '<img src="'.url($row->image).'" class="img-thumbnail" style="max-width: 80px;">'
                        : '';
                })
                ->addColumn('amount', function($row){
                    return '£' . number_format($row->amount, 2);
                })
                ->addColumn('status', function($row){
                    $checked = $row->is_active == 1 ? 'checked' : '';
                    return '<div class="form-check form-switch" dir="ltr">
                                <input type="checkbox" class="form-check-input toggle-status" 
                                       data-id="'.$row->id.'" '.$checked.'>
                                <label class="form-check-label"></label>
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
                                            data-delete-url="' . route('giftcard-packages.destroy', $row->id) . '" 
                                            data-method="DELETE" 
                                            data-table="#packageTable">
                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                    </button>
                                </li>
                            </ul>
                        </div>
                    ';
                })
                ->rawColumns(['image','status','action'])
                ->make(true);
        }

        return view('admin.giftcard-packages.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:giftcard_packages,name',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        $data = new GiftcardPackage;
        $data->name = $request->name;
        $data->amount = $request->amount;
        $data->description = $request->description;

        if ($request->hasFile('image')) {
            $uploadedFile = $request->file('image');
            $randomName = mt_rand(10000000, 99999999) . '.webp';
            $destinationPath = public_path('uploads/giftcards/');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            Image::make($uploadedFile)
                ->encode('webp', 80)
                ->save($destinationPath . $randomName);

            $data->image = '/uploads/giftcards/' . $randomName;
        }

        if ($data->save()) {
            return response()->json(['message' => 'Package created successfully!'], 200);
        }

        return response()->json(['message' => 'Server error while creating package.'], 500);
    }

    public function edit($id)
    {
        $package = GiftcardPackage::findOrFail($id);

        return response()->json([
            'id' => $package->id,
            'name' => $package->name,
            'amount' => $package->amount,
            'description' => $package->description,
            'image' => $package->image ? url($package->image) : null,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:giftcard_packages,name,' . $request->codeid,
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        $data = GiftcardPackage::findOrFail($request->codeid);
        $data->name = $request->name;
        $data->amount = $request->amount;
        $data->description = $request->description;

        if ($request->hasFile('image')) {
            $uploadedFile = $request->file('image');

            if ($data->image && file_exists(public_path($data->image))) {
                @unlink(public_path($data->image));
            }

            $randomName = mt_rand(10000000, 99999999) . '.webp';
            $destinationPath = public_path('uploads/giftcards/');
            if (!file_exists($destinationPath)) mkdir($destinationPath, 0755, true);

            Image::make($uploadedFile)
                ->encode('webp', 80)
                ->save($destinationPath . $randomName);

            $data->image = '/uploads/giftcards/' . $randomName;
        }

        if ($data->save()) {
            return response()->json(['message' => 'Package updated successfully!'], 200);
        }

        return response()->json(['message' => 'Failed to update package.'], 500);
    }

    public function destroy($id)
    {
        $data = GiftcardPackage::find($id);
        
        if (!$data) {
            return response()->json(['message' => 'Package not found.'], 404);
        }

        if ($data->image && file_exists(public_path($data->image))) {
            @unlink(public_path($data->image));
        }

        if ($data->delete()) {
            return response()->json(['message' => 'Package deleted successfully.'], 200);
        }

        return response()->json(['message' => 'Failed to delete package.'], 500);
    }

    public function toggleStatus(Request $request)
    {
        $package = GiftcardPackage::find($request->id);

        if (!$package) {
            return response()->json(['message' => 'Package not found'], 404);
        }

        $package->is_active = $request->is_active;

        if ($package->save()) {
            return response()->json(['message' => 'Package status updated successfully'], 200);
        }

        return response()->json(['message' => 'Failed to update package status'], 500);
    }
}