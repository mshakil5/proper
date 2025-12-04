<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use Illuminate\Validation\Rule;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Plan::latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('is_recommended', fn($row) => $row->is_recommended ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>')
                ->addColumn('status', fn($row) => 
                    '<div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input toggle-status" id="status'.$row->id.'" data-id="'.$row->id.'" '.($row->status ? 'checked' : '').'>
                        <label class="custom-control-label" for="status'.$row->id.'"></label>
                    </div>'
                )
                ->addColumn('action', fn($row) => '
                    <button class="btn btn-sm btn-info edit" data-id="'.$row->id.'"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-danger delete" data-id="'.$row->id.'"><i class="fas fa-trash-alt"></i></button>
                ')
                ->rawColumns(['is_recommended','status','action'])
                ->make(true);
        }

        return view('admin.plans.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
          'name'   => ['required','string','max:255', Rule::unique('plans','name')],
          'amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails())
            return response()->json(['status'=>422,'errors'=>$validator->errors()],422);

        Plan::create([
            'name' => $request->name,
            'amount' => $request->amount,
            'is_recommended' => $request->is_recommended ?? 0,
            'status' => 1,
            'included_features' => is_string($request->included_features)
                ? json_decode($request->included_features, true)
                : ($request->included_features ?? []),
            'excluded_features' => is_string($request->excluded_features)
                ? json_decode($request->excluded_features, true)
                : ($request->excluded_features ?? []),
            'created_by' => auth()->id(),
        ]);

        return response()->json(['status'=>200,'message'=>'Created successfully']);
    }

    public function edit($id)
    {
        return response()->json(Plan::findOrFail($id));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', Rule::unique('plans', 'name')->ignore($request->codeid)],
            'amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails())
            return response()->json(['status'=>422,'errors'=>$validator->errors()],422);

        $plan = Plan::findOrFail($request->codeid);
        $plan->update([
            'name' => $request->name,
            'amount' => $request->amount,
            'is_recommended' => $request->is_recommended ?? 0,
            'included_features' => json_decode($request->included_features, true) ?? [],
            'excluded_features' => json_decode($request->excluded_features, true) ?? [],
            'updated_by' => auth()->id(),
        ]);

        return response()->json(['status'=>200,'message'=>'Updated successfully']);
    }

    public function destroy($id)
    {
        $plan = Plan::find($id);
        if (!$plan) return response()->json(['success'=>false,'message'=>'Not found'],404);
        $plan->delete();
        return response()->json(['success'=>true,'message'=>'Deleted successfully']);
    }

    public function toggleStatus(Request $request)
    {
        $plan = Plan::find($request->plan_id);
        if (!$plan) return response()->json(['status'=>404,'message'=>'Not found']);
        $plan->status = $request->status;
        $plan->save();
        return response()->json(['status'=>200,'message'=>'Status updated']);
    }
}