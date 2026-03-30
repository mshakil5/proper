@extends('admin.pages.master')
@section('title', 'Products')
@section('content')
    <div class="container-fluid" id="newBtnSection">
        <div class="row mb-3">
            <div class="col-auto">
                <button type="button" class="btn btn-primary" id="newBtn">
                    Add New Product
                </button>
            </div>
        </div>
    </div>

    <div class="container-fluid" id="addThisFormContainer">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1" id="cardTitle">Add New Product</h4>
                    </div>
                    <div class="card-body">
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" id="codeid" name="codeid">

                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Product Type <span class="text-danger">*</span></label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" id="typeMain" name="product_type" value="main" checked>
                                            <label class="form-check-label" for="typeMain">Main Product</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" id="typeSub" name="product_type" value="sub">
                                            <label class="form-check-label" for="typeSub">Sub Product</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Product Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="category_id" name="category_id">
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 d-none">
                                    <label class="form-label">Tag </label>
                                    <select class="form-control select2" id="tag_id" name="tag_id">
                                        <option value="">Select Tag</option>
                                        @foreach ($tags as $tag)
                                            <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Price <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="price" name="price" step="0.01" min="0">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">SKU Ref <span class="text-danger">*</span> <span class="text-muted">(from HubRise)</span></label>
                                    <input type="text" class="form-control" id="sku_ref" name="sku_ref" placeholder="e.g., 572">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Product Image</label>
                                    <input type="file" class="form-control" id="image" accept="image/*"
                                        onchange="previewImage(event, '#preview-image')">
                                </div>

                                <div class="col-md-6">
                                    <img id="preview-image" src="/placeholder.webp" alt="" class="img-thumbnail rounded"
                                        style="max-width: 200px; max-height: 200px;">
                                    <button type="button" class="btn btn-sm btn-danger mt-2" id="removeImageBtn" style="display:none;">
                                        Remove Image
                                    </button>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Short Description</label>
                                    <textarea class="form-control" id="short_description" name="short_description" rows="2"
                                        placeholder="Enter short description (optional)"></textarea>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Long Description</label>
                                    <textarea class="form-control summernote" id="long_description" name="long_description"
                                        placeholder="Enter long description (optional)"></textarea>
                                </div>

                                <div class="col-md-12">
                                    <hr>
                                    <h6 class="mb-3">Product Attribute (Optional)</h6>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="has_attribute" name="has_attribute" value="1" onchange="toggleAttributeFields()">
                                        <label class="form-check-label" for="has_attribute">
                                            This product has an attribute option (e.g., Make it a Meal)
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6" id="attributeNameField" style="display: none;">
                                    <label class="form-label">Attribute Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="attribute_name" name="attribute_name" 
                                        placeholder="e.g., Make it a Meal">
                                </div>

                                <div class="col-md-6" id="attributePriceField" style="display: none;">
                                    <label class="form-label">Extra Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">£</span>
                                        <input type="number" class="form-control" id="attribute_price" name="attribute_price" 
                                            step="0.01" min="0" placeholder="0.00">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" id="addBtn" class="btn btn-primary">Create</button>
                        <button type="button" id="FormCloseBtn" class="btn btn-light">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid" id="contentContainer">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#mainProducts" role="tab" data-type="main">
                            <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                            <span class="d-none d-sm-block">Main Products</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#subProducts" role="tab" data-type="sub">
                            <span class="d-block d-sm-none"><i class="fas fa-bars"></i></span>
                            <span class="d-none d-sm-block">Sub Products</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="mainProducts" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Main Products List</h5>
                            <div style="width:200px;">
                                <select id="filterCategory" class="form-control select2">
                                    <option value="">All Categories</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <table id="productTable" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Price</th>
                                    <th>Category</th>
                                    <th>Reference</th>
                                    <th>Stock</th>
                                    <th>In Option</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                    <div class="tab-pane fade" id="subProducts" role="tabpanel">
                        <div style="width:200px; margin-bottom: 15px;">
                            <select id="filterCategorySub" class="form-control select2">
                                <option value="">All Categories</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <table id="productTableSub" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Price</th>
                                    <th>Category</th>
                                    <th>Reference</th>
                                    <th>Stock</th>
                                    <th>In Option</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        let currentProductId = null;
        let currentProductType = 'main';
        let mainTable, subTable;

        function initDataTable(tableId, productType) {
            return $('#' + tableId).DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                destroy: true,
                order: [],
                ajax: {
                    url: "{{ route('allproducts') }}",
                    data: function (d) {
                        d.category_id = productType === 'main' ? $('#filterCategory').val() : $('#filterCategorySub').val();
                        d.product_type = productType;
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    { data: 'image', name: 'image', orderable:false, searchable:false },
                    { data: 'title', name: 'title' },
                    { data: 'price', name: 'price' },
                    { data: 'category_name', name: 'category_name' },
                    { data: 'sku_ref', name: 'sku_ref' },
                    { data: 'stock_status', name: 'stock_status', orderable: false, searchable: false },
                    { data: 'sidebar', name: 'sidebar', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        }

        $(document).ready(function() {
            mainTable = initDataTable('productTable', 'main');

            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                let type = $(e.target).data('type');

                currentProductType = type;

                if (type === 'sub') {
                    if ($.fn.DataTable.isDataTable('#productTableSub')) {
                        subTable.columns.adjust().draw(false);
                    } else {
                        subTable = initDataTable('productTableSub', 'sub');
                    }
                }

                if (type === 'main') {
                    mainTable.columns.adjust().draw(false);
                }
            });

            $('#filterCategory').change(function() {
                mainTable.ajax.reload();
            });

            $('#filterCategorySub').change(function() {
                subTable.ajax.reload();
            });

            $(document).on('change', '.toggle-status', function() {
                var product_id = $(this).data('id');
                var status = $(this).prop('checked') ? 1 : 0;
                $.post('/admin/products-status', {
                    _token: '{{ csrf_token() }}',
                    product_id: product_id,
                    status: status
                }, function(d) {
                    if (currentProductType === 'main') {
                        mainTable.ajax.reload();
                    } else {
                        subTable.ajax.reload();
                    }
                    showSuccess(d.message);
                }).fail(() => showError('Failed to update status'));
            });

            $(document).on('change', '.toggle-stock-status', function() {
                var product_id = $(this).data('id');
                var stock_status = $(this).prop('checked') ? 'in_stock' : 'out_of_stock';

                $.post('/admin/products-toggle-stock', {
                    _token: '{{ csrf_token() }}',
                    product_id: product_id,
                    stock_status: stock_status
                }, function(d) {
                    if (currentProductType === 'main') {
                        mainTable.ajax.reload(null,false);
                    } else {
                        subTable.ajax.reload(null,false);
                    }
                    showSuccess(d.message);
                }).fail(() => showError('Failed to update stock status'));
            });

            $(document).on('change', '.toggle-sidebar', function() {
                var product_id = $(this).data('id');
                var show_in_menu = $(this).prop('checked') ? 1 : 0;
                $.post('/admin/products-toggle-sidebar', {
                    _token: '{{ csrf_token() }}',
                    product_id: product_id,
                    show_in_menu: show_in_menu
                }, function(d) {
                    if (currentProductType === 'main') {
                        mainTable.ajax.reload();
                    } else {
                        subTable.ajax.reload();
                    }
                    showSuccess(d.message);
                }).fail(() => showError('Failed to update sidebar visibility'));
            });

            $("#addThisFormContainer").hide();
            $("#newBtn").click(function() {
                clearform();
                $("#addThisFormContainer").slideDown(300);
                $("#newBtn").hide();
                pageTop();
            });
            $("#FormCloseBtn").click(function() {
                $("#addThisFormContainer").slideUp(300);
                setTimeout(() => {
                    $("#newBtn").show();
                }, 300);
            });

            var url = "{{ URL::to('/admin/products') }}";
            var upurl = "{{ URL::to('/admin/products-update') }}";

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("#addBtn").click(function() {
                var form_data = new FormData();
                form_data.append("title", $("#title").val());
                form_data.append("category_id", $("#category_id").val());
                form_data.append("tag_id", $("#tag_id").val());
                form_data.append("price", $("#price").val());
                form_data.append("sku_ref", $("#sku_ref").val());
                form_data.append("short_description", $("#short_description").val());
                form_data.append("long_description", $(".summernote").summernote('code'));
                form_data.append("product_type", $('input[name="product_type"]:checked').val());
                form_data.append("has_attribute", $("#has_attribute").is(':checked') ? 1 : 0);
                form_data.append("attribute_name", $("#attribute_name").val());
                form_data.append("attribute_price", $("#attribute_price").val());
                
                var imageInput = document.getElementById('image');
                if (imageInput.files && imageInput.files[0]) {
                    form_data.append("image", imageInput.files[0]);
                }

                if ($(this).val() == 'Create') {
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: form_data,
                        contentType: false,
                        processData: false,
                        success: function(d) {
                            showSuccess(d.message);
                            $("#addThisFormContainer").slideUp(300);
                            setTimeout(() => {
                                $("#newBtn").show();
                            }, 300);
                            if (currentProductType === 'main') {
                                mainTable.ajax.reload();
                            } else {
                                subTable.ajax.reload();
                            }
                            clearform();
                        },
                        error: function(xhr) {
                            pageTop();
                            if (xhr.responseJSON?.errors) {
                                let errors = Object.values(xhr.responseJSON.errors).flat();
                                showError(errors[0]);
                            } else {
                                showError(xhr.responseJSON?.message ?? "Something went wrong!");
                            }
                        }
                    });
                }

                if ($(this).val() == 'Update') {
                    form_data.append("codeid", $("#codeid").val());
                    $.ajax({
                        url: upurl,
                        type: "POST",
                        data: form_data,
                        contentType: false,
                        processData: false,
                        success: function(d) {
                            showSuccess(d.message);
                            $("#addThisFormContainer").slideUp(300);
                            setTimeout(() => {
                                $("#newBtn").show();
                            }, 300);
                            if (currentProductType === 'main') {
                                mainTable.ajax.reload();
                            } else {
                                subTable.ajax.reload();
                            }
                            clearform();
                        },
                        error: function(xhr) {
                            pageTop();
                            if (xhr.responseJSON?.errors) {
                                let errors = Object.values(xhr.responseJSON.errors).flat();
                                showError(errors[0]);
                            } else {
                                showError(xhr.responseJSON?.message ?? "Something went wrong!");
                            }
                        }
                    });
                }
            });

            $(document).on('click', '#EditBtn', function() {
                $("#cardTitle").text('Update Product');
                codeid = $(this).attr('rid');
                currentProductId = codeid;
                $.get(url + '/' + codeid + '/edit', {}, function(d) {
                    populateForm(d);
                });
            });

            $("#removeImageBtn").click(function(e) {
                e.preventDefault();
                if (!currentProductId) return;
                
                $.post('/admin/products/' + currentProductId + '/remove-image', {
                    _token: '{{ csrf_token() }}'
                }, function(d) {
                    $("#preview-image").attr('src', '/placeholder.webp');
                    $("#removeImageBtn").hide();
                    $("#image").val('');
                    showSuccess(d.message);
                }).fail(() => showError('Failed to remove image'));
            });

            function populateForm(data) {
                $("#title").val(data.title);
                $("#category_id").val(data.category_id).trigger('change');
                $("#tag_id").val(data.tag_id).trigger('change');
                $("#price").val(data.price);
                $("#sku_ref").val(data.sku_ref);
                $("#short_description").val(data.short_description);
                $(".summernote").summernote('code', data.long_description);
                
                let productType = data.status == 1 ? 'main' : 'sub';
                $('input[name="product_type"][value="' + productType + '"]').prop('checked', true);
                
                $("#has_attribute").prop('checked', data.has_attribute == 1);
                $("#attribute_name").val(data.attribute_name || '');
                $("#attribute_price").val(data.attribute_price || '0.00');
                
                toggleAttributeFields();
                
                $("#codeid").val(data.id);
                
                $("#preview-image").attr('src', data.image);
                
                if (data.image && data.image != '/placeholder.webp') {
                    $("#removeImageBtn").show();
                } else {
                    $("#removeImageBtn").hide();
                }
                
                $("#addBtn").val('Update').html('Update');
                $("#addThisFormContainer").show();
                $("#newBtn").hide();
                pageTop();
            }

            function clearform() {
                $('#createThisForm')[0].reset();
                $(".summernote").summernote('code', '');
                $("#category_id").val(null).trigger('change');
                $("#tag_id").val(null).trigger('change');
                $("#preview-image").attr('src', '/placeholder.webp');
                $("#removeImageBtn").hide();
                $('input[name="product_type"][value="main"]').prop('checked', true);
                $("#has_attribute").prop('checked', false);
                $("#attribute_name").val('');
                $("#attribute_price").val('');
                toggleAttributeFields();
                $("#addBtn").val('Create').html('Create');
                $("#cardTitle").text('Add new Product');
                currentProductId = null;
            }
        });

        function toggleAttributeFields() {
            if ($("#has_attribute").is(':checked')) {
                $("#attributeNameField").slideDown(200);
                $("#attributePriceField").slideDown(200);
            } else {
                $("#attributeNameField").slideUp(200);
                $("#attributePriceField").slideUp(200);
            }
        }
    </script>

@endsection