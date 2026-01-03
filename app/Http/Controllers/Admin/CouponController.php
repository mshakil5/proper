<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use DataTables;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $coupons = Coupon::select(['id', 'code', 'name', 'image', 'discount_type', 'discount_value', 'min_order_amount', 'start_date', 'end_date', 'is_active', 'used_count', 'max_uses'])->orderBy('id', 'desc');
            
            return DataTables::of($coupons)
                ->addIndexColumn()
                ->addColumn('image', function ($row) {
                    return $row->image
                        ? '<img src="'.url($row->image).'" class="img-thumbnail" style="max-width: 80px;">'
                        : '';
                })
                ->addColumn('discount_info', function ($row) {
                    $discount = $row->discount_type === 'percent' 
                        ? $row->discount_value . '%' 
                        : '$' . number_format($row->discount_value, 2);
                    
                    $minAmount = $row->min_order_amount > 0 
                        ? '<br><small>Min: $' . number_format($row->min_order_amount, 2) . '</small>' 
                        : '';
                    
                    $typeBadge = $row->discount_type === 'percent' 
                        ? '<span class="badge bg-info">Percent</span>' 
                        : '<span class="badge bg-success">Fixed</span>';
                    
                    return $typeBadge . '<br><strong>' . $discount . '</strong>' . $minAmount;
                })
                ->addColumn('usage', function ($row) {
                    $uses = $row->used_count;
                    $max = $row->max_uses ?? '∞';
                    $maxText = $max === '∞' ? '∞' : $max;
                    return $uses . ' / ' . $maxText;
                })
                ->addColumn('dates', function ($row) {
                    $start = $row->start_date 
                        ? '<small><strong>Start:</strong> ' . $row->start_date->format('d M, Y') . '</small><br>'
                        : '<small><strong>Start:</strong> Immediate</small><br>';
                    
                    $end = $row->end_date 
                        ? '<small><strong>End:</strong> ' . $row->end_date->format('d M, Y') . '</small>'
                        : '<small><strong>End:</strong> No expiry</small>';
                    
                    return $start . $end;
                })
                ->addColumn('status', function ($row) {
                    $checked = $row->is_active ? 'checked' : '';
                    $expired = $row->end_date && $row->end_date < now();
                    $maxUsed = $row->max_uses && $row->used_count >= $row->max_uses;
                    
                    $badge = '';
                    if ($expired) {
                        $badge = '<span class="badge bg-danger ms-2">Expired</span>';
                    } elseif ($maxUsed) {
                        $badge = '<span class="badge bg-warning ms-2">Fully Used</span>';
                    }
                    
                    return '<div class="d-flex align-items-center">
                        <div class="form-check form-switch" dir="ltr">
                            <input type="checkbox" class="form-check-input toggle-status" 
                                  id="customSwitchStatus'.$row->id.'" data-id="'.$row->id.'" '.$checked.'>
                            <label class="form-check-label" for="customSwitchStatus'.$row->id.'"></label>
                        </div>
                        '.$badge.'
                    </div>';
                })
                ->addColumn('action', function ($row) {
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
                                            data-delete-url="' . route('coupons.delete', $row->id) . '" 
                                            data-method="DELETE" 
                                            data-table="#couponsTable">
                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                    </button>
                                </li>
                            </ul>
                        </div>
                    ';
                })
                ->rawColumns(['image', 'discount_info', 'usage', 'dates', 'status', 'action'])
                ->make(true);
        }

        return view('admin.coupon.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:coupons,code|uppercase|alpha_dash',
            'name' => 'required',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0.01',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'code.required' => 'Coupon code is required.',
            'code.unique' => 'This coupon code already exists.',
            'code.uppercase' => 'Coupon code must be uppercase.',
            'discount_value.min' => 'Discount value must be greater than 0.'
        ]);

        $data = new Coupon;
        $data->code = strtoupper($request->code);
        $data->name = $request->name;
        $data->description = $request->description;
        $data->discount_type = $request->discount_type;
        $data->discount_value = $request->discount_value;
        $data->min_order_amount = $request->min_order_amount ?? 0;
        $data->max_uses = $request->max_uses;
        $data->start_date = $request->start_date;
        $data->end_date = $request->end_date;

        if ($request->hasFile('image')) {
            $uploadedFile = $request->file('image');
            $randomName = mt_rand(10000000, 99999999) . '.webp';
            $destinationPath = public_path('uploads/coupons/');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            Image::make($uploadedFile)
                ->encode('webp', 80)
                ->save($destinationPath . $randomName);

            $data->image = '/uploads/coupons/' . $randomName;
        }

        if ($data->save()) {
            return response()->json([
                'message' => 'Coupon created successfully!',
                'coupon' => $data 
            ], 200);
        }

        return response()->json([
            'message' => 'Server error while creating coupon.'
        ], 500);
    }

    public function edit($id)
    {
        $coupon = Coupon::findOrFail($id);

        return response()->json([
            'id' => $coupon->id,
            'code' => $coupon->code,
            'name' => $coupon->name,
            'description' => $coupon->description,
            'discount_type' => $coupon->discount_type,
            'discount_value' => $coupon->discount_value,
            'min_order_amount' => $coupon->min_order_amount,
            'max_uses' => $coupon->max_uses,
            'start_date' => $coupon->start_date ? Carbon::parse($coupon->start_date)->format('Y-m-d') : null,
            'end_date' => $coupon->end_date ? Carbon::parse($coupon->end_date)->format('Y-m-d') : null,
            'image' => $coupon->image ? url($coupon->image) : null,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:coupons,code,' . $request->codeid . '|uppercase|alpha_dash',
            'name' => 'required',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0.01',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'code.required' => 'Coupon code is required.',
            'code.unique' => 'This coupon code already exists.',
            'code.uppercase' => 'Coupon code must be uppercase.',
            'discount_value.min' => 'Discount value must be greater than 0.'
        ]);

        $data = Coupon::findOrFail($request->codeid);
        $data->code = strtoupper($request->code);
        $data->name = $request->name;
        $data->description = $request->description;
        $data->discount_type = $request->discount_type;
        $data->discount_value = $request->discount_value;
        $data->min_order_amount = $request->min_order_amount ?? 0;
        $data->max_uses = $request->max_uses;
        $data->start_date = $request->start_date;
        $data->end_date = $request->end_date;
        if ($request->hasFile('image')) {
            $uploadedFile = $request->file('image');

            // Delete old image if exists
            if ($data->image && file_exists(public_path($data->image))) {
                @unlink(public_path($data->image));
            }

            $randomName = mt_rand(10000000, 99999999) . '.webp';
            $destinationPath = public_path('uploads/coupons/');
            if (!file_exists($destinationPath)) mkdir($destinationPath, 0755, true);

            Image::make($uploadedFile)
                ->encode('webp', 80)
                ->save($destinationPath . $randomName);

            $data->image = '/uploads/coupons/' . $randomName;
        }

        if ($data->save()) {
            return response()->json([
                'message' => 'Coupon updated successfully!'
            ], 200);
        }

        return response()->json([
            'message' => 'Failed to update coupon. Please try again.'
        ], 500);
    }

    public function delete($id)
    {
        $data = Coupon::find($id);
        
        if (!$data) {
            return response()->json([
                'message' => 'Coupon not found.'
            ], 404);
        }

        // Delete image if exists
        if ($data->image && file_exists(public_path($data->image))) {
            @unlink(public_path($data->image));
        }

        if ($data->delete()) {
            return response()->json([
                'message' => 'Coupon deleted successfully.'
            ], 200);
        }

        return response()->json([
            'message' => 'Failed to delete coupon.'
        ], 500);
    }

    public function toggleStatus(Request $request)
    {
        $coupon = Coupon::find($request->coupon_id);

        if (!$coupon) {
            return response()->json([
                'message' => 'Coupon not found'
            ], 404);
        }

        $coupon->is_active = $request->status;

        if ($coupon->save()) {
            return response()->json([
                'message' => 'Coupon status updated successfully'
            ], 200);
        }

        return response()->json([
            'message' => 'Failed to update coupon status'
        ], 500);
    }

    public function validateCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string|uppercase',
            'amount' => 'required|numeric|min:0'
        ]);

        $coupon = Coupon::where('code', $request->code)->first();

        if (!$coupon) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid coupon code'
            ], 404);
        }

        if (!$coupon->is_active) {
            return response()->json([
                'valid' => false,
                'message' => 'This coupon is inactive'
            ], 400);
        }

        if ($coupon->end_date && $coupon->end_date < now()) {
            return response()->json([
                'valid' => false,
                'message' => 'This coupon has expired'
            ], 400);
        }

        if ($coupon->max_uses && $coupon->used_count >= $coupon->max_uses) {
            return response()->json([
                'valid' => false,
                'message' => 'This coupon has reached its maximum usage limit'
            ], 400);
        }

        if ($coupon->min_order_amount > 0 && $request->amount < $coupon->min_order_amount) {
            return response()->json([
                'valid' => false,
                'message' => 'Minimum order amount of $' . number_format($coupon->min_order_amount, 2) . ' required'
            ], 400);
        }

        $discount = $coupon->calculateDiscount($request->amount);

        return response()->json([
            'valid' => true,
            'discount' => $discount,
            'discount_type' => $coupon->discount_type,
            'discount_value' => $coupon->discount_value,
            'coupon' => $coupon
        ], 200);
    }
}