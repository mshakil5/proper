<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Credential;
use Illuminate\Http\Request;
use DataTables;

class CredentialController extends Controller
{
    public function index(Request $request)
    {
        Credential::firstOrCreate(['gateway' => 'Paypal'], ['client_id' => '', 'client_secret' => '', 'mode' => 'sandbox']);
        Credential::firstOrCreate(['gateway' => 'Stripe'], ['client_id' => '', 'client_secret' => '', 'mode' => 'sandbox']);

        if ($request->ajax()) {
            $credentials = Credential::select(['id', 'gateway', 'client_id', 'client_secret', 'mode']);
            return DataTables::of($credentials)
                ->addIndexColumn()
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
                            </ul>
                        </div>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.credentials.index');
    }

    public function edit($id)
    {
        $credential = Credential::findOrFail($id);

        return response()->json([
            'id' => $credential->id,
            'gateway' => $credential->gateway,
            'client_id' => $credential->client_id,
            'client_secret' => $credential->client_secret,
            'mode' => $credential->mode,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'client_id' => 'nullable|string',
            'client_secret' => 'nullable|string',
            'mode' => 'required|in:sandbox,live',
        ]);

        $credential = Credential::findOrFail($request->id);
        $credential->update([
            'client_id' => $request->client_id,
            'client_secret' => $request->client_secret,
            'mode' => $request->mode,
        ]);

        return response()->json(['message' => 'Credential updated successfully!'], 200);
    }
}