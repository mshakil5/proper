@extends('admin.pages.master')
@section('title', 'Product Options - ' . $product->title)
@section('content')

<div class="container-fluid">
    <div class="row mb-3" id="newBtnSection">
        <div class="col-2 mt-2">
            <a href="{{ url()->previous() }}" class="btn btn-primary"> Back</a>
        </div>
        <div class="col">
            <h4>Options for: <strong>{{ $product->title }}</strong></h4>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-primary" id="newOptionBtn">
                <i class="ri-add-line"></i> Add New Option
            </button>
        </div>
    </div>

    <div class="row justify-content-center mb-4" id="formContainer" style="display:none;">
        <div class="col-xl-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0" id="formTitle">Add New Option</h5>
                </div>
                <div class="card-body">
                    <form id="optionForm">
                        @csrf
                        <input type="hidden" id="option_id" name="option_id">
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="category_id" name="category_id" required onchange="loadCategoryProducts()">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $cat)
                                        @if($cat->id != $product->category_id)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <small class="text-muted" id="categoryError" style="color: red; display: none;"></small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Option Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="e.g., Choose Burger" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Selection Type <span class="text-danger">*</span></label>
                                <select class="form-control" id="type" name="type" required onchange="toggleMaxSelect()">
                                    <option value="single">Single Select (Radio)</option>
                                    <option value="multiple">Multiple Select (Checkbox)</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Max Selections</label>
                                <input type="number" class="form-control" id="max_select" name="max_select" value="1" min="1">
                            </div>

                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_required" name="is_required" value="1">
                                    <label class="form-check-label" for="is_required">
                                        Required (Must select an option)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-3">
                            <div class="col-12">
                                <h6 class="mb-3">Products from Category</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm" id="productsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Product Name</th>
                                                <th>Base Price</th>
                                                <th>Override Price</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="productsTableBody">
                                            <tr id="emptyRow">
                                                <td colspan="4" class="text-center text-muted">Select a category to load products</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-end">
                    <button type="button" class="btn btn-primary" id="saveOptionBtn">Save Option</button>
                    <button type="button" class="btn btn-light" id="cancelFormBtn">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Options List</h5>
        </div>
        <div class="card-body">
            <table id="optionsTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Option Name</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>Max Select</th>
                        <th>Required</th>
                        <th>Items</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
let currentOptionId = null;
let selectedProducts = {};

$(document).ready(function() {
    initDataTable();
    bindEvents();
});

function initDataTable() {
    $('#optionsTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        ajax: {
            url: "{{ route('product.options', $product->id) }}",
            type: 'GET'
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'name', name: 'name'},
            {data: 'category_name', name: 'category_name'},
            {data: 'type_badge', name: 'type_badge', orderable: false, searchable: false},
            {data: 'max_select', name: 'max_select'},
            {data: 'required_badge', name: 'required_badge', orderable: false, searchable: false},
            {data: 'items_count', name: 'items_count', orderable: false, searchable: false},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });
}

function bindEvents() {
    $('#newOptionBtn').click(function() {
        clearForm();
        $('#formContainer').slideDown(300);
        $('#newBtnSection').slideUp(300);
        pageTop();
    });

    $('#cancelFormBtn').click(function() {
        $('#formContainer').slideUp(300);
        $('#newBtnSection').slideDown(300);
    });

    $(document).on('click', '.editBtn', function() {
        currentOptionId = $(this).data('id');
        $.get('/admin/product-options/' + currentOptionId + '/edit', function(data) {
            clearForm();
            $('#option_id').val(data.id);
            $('#category_id').val(data.category_id).trigger('change');
            $('#name').val(data.name);
            $('#type').val(data.type).trigger('change');
            $('#max_select').val(data.max_select);
            $('#is_required').prop('checked', data.is_required == 1);

            loadCategoryProducts(data.id);
            
            $('#formTitle').text('Edit Option');
            $('#formContainer').slideDown(300);
            $('#newBtnSection').slideUp(300);
            pageTop();
        });
    });

    $('#saveOptionBtn').click(function() {
        if (Object.keys(selectedProducts).length === 0) {
            showError('Please select at least one product');
            return;
        }

        let optionId = $('#option_id').val();
        let url = optionId ? '/admin/product-options/' + optionId : '/admin/product-options';
        let formData = new FormData($('#optionForm')[0]);

        for (let productId in selectedProducts) {
            formData.append('products[' + productId + ']', selectedProducts[productId]);
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(d) {
                showSuccess(d.message);
                $('#formContainer').slideUp(300);
                $('#newBtnSection').slideDown(300);
                reloadTable('#optionsTable');
                clearForm();
            },
            error: function(xhr) {
                pageTop();
                if (xhr.status === 422 && xhr.responseJSON?.message) {
                    showError(xhr.responseJSON.message);
                } else if (xhr.responseJSON?.errors) {
                    let errors = Object.values(xhr.responseJSON.errors).flat();
                    showError(errors[0]);
                } else {
                    showError(xhr.responseJSON?.message ?? 'Something went wrong!');
                }
            }
        });
    });

    $(document).on('change', '.price-input', function() {
        let productId = $(this).data('product-id');
        let price = $(this).val();
        
        if (price && price !== '') {
            selectedProducts[productId] = parseFloat(price);
        } else {
            delete selectedProducts[productId];
        }
    });

    $(document).on('click', '.delete-product-row', function(e) {
        e.preventDefault();
        let productId = $(this).data('product-id');
        
        delete selectedProducts[productId];
        $(this).closest('tr').remove();
        
        let remainingRows = $('#productsTableBody tr:not(#emptyRow)').length;
        if (remainingRows === 0) {
            $('#productsTableBody').html('<tr id="emptyRow"><td colspan="4" class="text-center text-muted">No products selected</td></tr>');
        }
    });
}

function loadCategoryProducts(optionId = null) {
    let categoryId = $('#category_id').val();
    if (!categoryId) {
        $('#productsTableBody').html('<tr id="emptyRow"><td colspan="4" class="text-center text-muted">Select a category to load products</td></tr>');
        selectedProducts = {};
        return;
    }

    let productId = "{{ $product->id }}";
    let url = '/admin/product/' + productId + '/category/' + categoryId + '/products';
    if (optionId) {
        url += '/' + optionId;
    }
    
    $.get(url, function(products) {
        $('#productsTableBody').html('');
        selectedProducts = {};
        
        if (products.length === 0) {
            $('#productsTableBody').html('<tr id="emptyRow"><td colspan="4" class="text-center text-muted">No products in this category</td></tr>');
            return;
        }

        let html = '';
        
        products.forEach(p => {
            let rowClass = p.is_selected ? 'table-success' : '';
            
            html += `
                <tr data-product-id="${p.id}" class="${rowClass}">
                    <td>${p.title}</td>
                    <td>£${parseFloat(p.price).toFixed(2)}</td>
                    <td>
                        <input type="number" class="form-control form-control-sm price-input" 
                            data-product-id="${p.id}" 
                            value="${parseFloat(p.override_price).toFixed(2)}" 
                            step="0.01" 
                            min="0" 
                            required>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger delete-product-row" 
                            data-product-id="${p.id}">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </td>
                </tr>
            `;

            selectedProducts[p.id] = parseFloat(p.override_price);
        });
        
        $('#productsTableBody').html(html);

    }).fail(function() {
        showError('Failed to load products');
    });
}

function toggleMaxSelect() {
    let type = $('#type').val();
    if (type === 'single') {
        $('#max_select').val(1).addClass('d-none');
        $('#max_select').closest('.col-md-6').find('label').addClass('d-none');
    } else {
        $('#max_select').removeClass('d-none');
        $('#max_select').closest('.col-md-6').find('label').removeClass('d-none');
    }
}

function clearForm() {
    $('#optionForm')[0].reset();
    $('#option_id').val('');
    $('#formTitle').text('Add New Option');
    $('#productsTableBody').html('<tr id="emptyRow"><td colspan="4" class="text-center text-muted">Select a category to load products</td></tr>');
    currentOptionId = null;
    selectedProducts = {};
    toggleMaxSelect();
    $('#category_id').val(null).trigger('change');
}
</script>
@endsection