<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductOptionItem;
use App\Models\Category;
use Illuminate\Http\Request;
use DataTables;

class ProductOptionController extends Controller
{
    public function index($id)
    {
        $product = Product::findOrFail($id);
        
        if (request()->ajax()) {
            $options = ProductOption::where('product_id', $id)
                ->with('category')
                ->latest()
                ->get();

            return DataTables::of($options)
                ->addIndexColumn()
                ->addColumn('category_name', function($row) {
                    return $row->category->name ?? '-';
                })
                ->addColumn('type_badge', function($row) {
                    $badge = $row->type === 'single' ? 'info' : 'warning';
                    return '<span class="badge bg-'.$badge.'">'.ucfirst($row->type).'</span>';
                })
                ->addColumn('items_count', function($row) {
                    return '<span class="badge bg-secondary">'.$row->items()->count().'</span>';
                })
                ->addColumn('required_badge', function($row) {
                    return $row->is_required ? '<span class="badge bg-danger">Required</span>' : '<span class="badge bg-light">Optional</span>';
                })
                ->addColumn('action', function($row) {
                    return '
                        <div class="dropdown">
                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ri-more-fill align-middle"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <button class="dropdown-item editBtn" data-id="'.$row->id.'">
                                        <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit
                                    </button>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <button class="dropdown-item deleteBtn" 
                                        data-delete-url="'.route('product-option.destroy',$row->id).'" 
                                        data-method="DELETE" 
                                        data-table="#optionsTable">
                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                    </button>
                                </li>
                            </ul>
                        </div>
                    ';
                })
                ->rawColumns(['type_badge', 'items_count', 'required_badge', 'action'])
                ->make(true);
        }

        $categories = Category::where('show_in_menu', 1)->get();
        return view('admin.product.option', compact('product', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:single,multiple',
            'max_select' => 'required|integer|min:1',
            'is_required' => 'nullable|boolean',
            'products' => 'required|array|min:1'
        ]);

        $exists = ProductOption::where('product_id', $request->product_id)
            ->where('category_id', $request->category_id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'This category is already added for this product!'], 422);
        }

        $option = ProductOption::create([
            'product_id' => $request->product_id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'type' => $request->type,
            'max_select' => $request->type === 'multiple' ? $request->max_select : 1,
            'is_required' => $request->is_required ? 1 : 0
        ]);

        foreach ($request->products as $productId => $price) {
            ProductOptionItem::create([
                'product_option_id' => $option->id,
                'product_id' => $productId,
                'override_price' => $price
            ]);
        }

        return response()->json(['message' => 'Option created successfully!'], 200);
    }

    public function edit($id)
    {
        $option = ProductOption::with('items.product')->findOrFail($id);
        return response()->json($option);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:single,multiple',
            'max_select' => 'required|integer|min:1',
            'is_required' => 'nullable|boolean',
            'products' => 'required|array|min:1'
        ]);

        $option = ProductOption::findOrFail($id);

        $exists = ProductOption::where('product_id', $option->product_id)
            ->where('category_id', $request->category_id)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'This category is already added for this product!'], 422);
        }

        $option->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'type' => $request->type,
            'max_select' => $request->type === 'multiple' ? $request->max_select : 1,
            'is_required' => $request->is_required ? 1 : 0
        ]);

        $option->items()->delete();

        foreach ($request->products as $productId => $price) {
            ProductOptionItem::create([
                'product_option_id' => $option->id,
                'product_id' => $productId,
                'override_price' => $price
            ]);
        }

        return response()->json(['message' => 'Option updated successfully!'], 200);
    }

    public function destroy($id)
    {
        ProductOption::findOrFail($id)->delete();
        return response()->json(['message' => 'Option deleted successfully!'], 200);
    }

    public function getCategoryProducts($productId, $categoryId)
    {
        $products = Product::where('category_id', $categoryId)
            ->where('status', 1)
            ->select('id', 'title', 'price')
            ->where('show_in_menu', 1)
            ->get();

        return response()->json($products);
    }
}