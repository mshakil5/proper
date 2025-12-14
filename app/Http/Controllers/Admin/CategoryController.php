<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use DataTables;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $categories = Category::select(['id','sl','name','description','status','show_in_menu'])->orderBy('sl');
            return DataTables::of($categories)
                ->addIndexColumn()
                ->addColumn('status', function($row){
                    $checked = $row->status == 1 ? 'checked' : '';
                    return '<div class="form-check form-switch" dir="ltr">
                                <input type="checkbox" class="form-check-input toggle-status" 
                                       id="customSwitchStatus'.$row->id.'" data-id="'.$row->id.'" '.$checked.'>
                                <label class="form-check-label" for="customSwitchStatus'.$row->id.'"></label>
                            </div>';
                })
                ->addColumn('sidebar', function($row){
                    $checked = $row->show_in_menu == 1 ? 'checked' : '';
                    return '<div class="form-check form-switch" dir="ltr">
                                <input type="checkbox" class="form-check-input toggle-sidebar" 
                                       id="customSwitchSidebar'.$row->id.'" data-id="'.$row->id.'" '.$checked.'>
                                <label class="form-check-label" for="customSwitchSidebar'.$row->id.'"></label>
                            </div>';
                })
                ->addColumn('sl', function($row){
                    return '<span class="serial-text">'.$row->sl.'</span>';
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
                                        data-delete-url="'.route('categories.destroy',$row->id).'" 
                                        data-method="DELETE" 
                                        data-table="#categoryTable">
                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                    </button>
                                </li>
                            </ul>
                        </div>
                    ';
                })
                ->rawColumns(['status','sidebar','sl','action'])
                ->make(true);
        }

        $categories = Category::orderBy('sl')->get();
        return view('admin.category.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories,name',
            'description' => 'nullable'
        ]);

        $lastSl = Category::max('sl');
        
        Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'sl' => $lastSl ? $lastSl + 1 : 1,
            'status' => 1,
            'show_in_menu' => 0
        ]);

        return response()->json(['message' => 'Category created successfully!'], 200);
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories,name,'.$request->codeid,
            'description' => 'nullable'
        ]);

        $category = Category::findOrFail($request->codeid);
        $category->update([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return response()->json(['message' => 'Category updated successfully!'], 200);
    }

    public function destroy($id)
    {
        Category::findOrFail($id)->delete();
        return response()->json(['message' => 'Category deleted successfully.'], 200);
    }

    public function toggleStatus(Request $request)
    {
        $category = Category::findOrFail($request->category_id);
        $category->update(['status' => $request->status]);
        return response()->json(['message' => 'Category status updated successfully.'], 200);
    }

    public function toggleSidebar(Request $request)
    {
        $category = Category::findOrFail($request->category_id);
        $category->update(['show_in_menu' => $request->show_in_menu]);
        return response()->json(['message' => 'Visibility updated successfully.'], 200);
    }

    public function updateCategoryOrder(Request $request)
    {
        $order = $request->order;
        foreach($order as $index => $id){
            Category::where('id', $id)->update(['sl' => $index + 1]);
        }
        return response()->json(['success' => true, 'message' => 'Category order updated successfully!']);
    }
}