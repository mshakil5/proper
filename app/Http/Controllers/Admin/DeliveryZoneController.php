<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DeliveryZoneController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $zones = DeliveryZone::orderByDesc('id');
            return DataTables::of($zones)
                ->addIndexColumn()
                ->addColumn('is_active', function($row){
                    $checked = $row->is_active == 1 ? 'checked' : '';
                    return '<div class="form-check form-switch" dir="ltr">
                                <input type="checkbox" class="form-check-input toggle-status" 
                                       id="customSwitchStatus'.$row->id.'" data-id="'.$row->id.'" '.$checked.'>
                                <label class="form-check-label" for="customSwitchStatus'.$row->id.'"></label>
                            </div>';
                })
                ->addColumn('action', function($row){
                    return '
                        <div class="dropdown">
                            <button class="btn btn-soft-secondary btn-sm" data-bs-toggle="dropdown"><i class="ri-more-fill"></i></button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><button class="dropdown-item EditBtn" data-id="'.$row->id.'"><i class="ri-pencil-fill me-2"></i>Edit</button></li>
                                <li class="dropdown-divider"></li>
                                <li><button class="dropdown-item deleteBtn" data-delete-url="'.route('delivery-zone.delete', $row->id).'"><i class="ri-delete-bin-fill me-2"></i>Delete</button></li>
                            </ul>
                        </div>';
                })
                ->rawColumns(['is_active', 'action'])
                ->make(true);
        }

        return view('admin.delivery_zones.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'postcode_prefix' => 'required|string|max:10|unique:delivery_zones,postcode_prefix',
            'delivery_charge' => 'required|numeric|min:0',
        ]);

        $zone = DeliveryZone::create([
            'postcode_prefix' => strtoupper($request->postcode_prefix),
            'delivery_charge' => $request->delivery_charge,
            'is_active' => 1,
        ]);

        return response()->json(['message' => 'Delivery zone created successfully.'], 201);
    }

    public function edit($id)
    {
        $zone = DeliveryZone::findOrFail($id);
        return response()->json($zone);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:delivery_zones,id',
            'postcode_prefix' => 'required|string|max:10|unique:delivery_zones,postcode_prefix,'.$request->id,
            'delivery_charge' => 'required|numeric|min:0',
        ]);

        $zone = DeliveryZone::findOrFail($request->id);
        $zone->update([
            'postcode_prefix' => strtoupper($request->postcode_prefix),
            'delivery_charge' => $request->delivery_charge,
        ]);

        return response()->json(['message' => 'Delivery zone updated successfully.'], 200);
    }

    public function toggleStatus(Request $request)
    {
        $zone = DeliveryZone::findOrFail($request->zone_id);
        $zone->update(['is_active' => $request->is_active]);
        return response()->json(['message' => 'Delivery zone status updated successfully.'], 200);
    }

    public function destroy($id)
    {
        $zone = DeliveryZone::findOrFail($id);
        $zone->delete();
        return response()->json(['message' => 'Delivery zone deleted successfully.'], 200);
    }
}