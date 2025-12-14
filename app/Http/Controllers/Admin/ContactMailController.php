<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactEmail;
use DataTables;

class ContactMailController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $emails = ContactEmail::select(['id', 'email', 'email_holder', 'created_at'])->latest();
            
            return DataTables::of($emails)
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
                                <li class="dropdown-divider"></li>
                                <li>
                                    <button class="dropdown-item deleteBtn" 
                                        data-delete-url="'.route('contactemail.destroy',$row->id).'" 
                                        data-method="DELETE" 
                                        data-table="#contactEmailTable">
                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                    </button>
                                </li>
                            </ul>
                        </div>
                    ';
                })
                ->editColumn('created_at', function($row){
                    return $row->created_at->format('d M Y');
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.contact_email.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:contact_emails,email',
            'email_holder' => 'required|string|max:255',
        ]);

        ContactEmail::create([
            'email' => $request->email,
            'email_holder' => $request->email_holder,
            'created_by' => auth()->id()
        ]);

        return response()->json(['message' => 'Contact email created successfully!'], 200);
    }

    public function edit($id)
    {
        $email = ContactEmail::findOrFail($id);
        return response()->json($email);
    }

    public function update(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:contact_emails,email,'.$request->codeid,
            'email_holder' => 'required|string|max:255',
        ]);

        $email = ContactEmail::findOrFail($request->codeid);
        $email->update([
            'email' => $request->email,
            'email_holder' => $request->email_holder,
            'updated_by' => auth()->id()
        ]);

        return response()->json(['message' => 'Contact email updated successfully!'], 200);
    }

    public function destroy($id)
    {
        ContactEmail::findOrFail($id)->delete();
        return response()->json(['message' => 'Contact email deleted successfully.'], 200);
    }
}