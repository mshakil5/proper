<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaqQuestion;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FAQController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $faqs = FaqQuestion::orderByDesc('id');
            return DataTables::of($faqs)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    return '
                        <div class="dropdown">
                            <button class="btn btn-soft-secondary btn-sm" data-bs-toggle="dropdown"><i class="ri-more-fill"></i></button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><button class="dropdown-item EditBtn" data-id="'.$row->id.'"><i class="ri-pencil-fill me-2"></i>Edit</button></li>
                                <li class="dropdown-divider"></li>
                                <li><button class="dropdown-item deleteBtn" data-delete-url="'.route('faq.delete', $row->id).'"><i class="ri-delete-bin-fill me-2"></i>Delete</button></li>
                            </ul>
                        </div>';
                })
                ->rawColumns(['action', 'answer'])
                ->make(true);
        }

        return view('admin.faq_questions.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        $faq = FaqQuestion::create([
            'question' => $request->question,
            'answer' => $request->answer,
            'created_by' => auth()->id(),
        ]);

        return response()->json(['message' => 'FAQ created successfully.'], 201);
    }

    public function edit($id)
    {
        $faq = FaqQuestion::findOrFail($id);
        return response()->json($faq);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:faq_questions,id',
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        $faq = FaqQuestion::findOrFail($request->id);
        $faq->update([
            'question' => $request->question,
            'answer' => $request->answer,
            'updated_by' => auth()->id(),
        ]);

        return response()->json(['message' => 'FAQ updated successfully.'], 200);
    }

    public function destroy($id)
    {
        $faq = FaqQuestion::findOrFail($id);
        $faq->delete();
        return response()->json(['message' => 'FAQ deleted successfully.'], 200);
    }
}