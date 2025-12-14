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
                                    <label class="form-label">Product Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="category_id" name="category_id">
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
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
            <div class="card-header d-flex justify-content-between align-items-center">
    <h4 class="card-title mb-0">Products List</h4>

    <div style="width:200px;">
        <select id="filterCategory" class="form-control select2">
            <option value="">All Categories</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>
</div>

            <div class="card-body">
                <table id="productTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Price</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Show in Menu</th>
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
        let currentProductId = null;

        $(document).ready(function() {
            $('#productTable').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                ajax: {
                    url: "{{ route('allproducts') }}",
                    data: function (d) {
                        d.category_id = $('#filterCategory').val(); 
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    { data: 'image', name: 'image', orderable:false, searchable:false },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'price',
                        name: 'price'
                    },
                    { data: 'category_name', name: 'category_name' },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'sidebar',
                        name: 'sidebar',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#filterCategory').change(function() {
                reloadTable('#productTable');
            });

            $(document).on('change', '.toggle-status', function() {
                var product_id = $(this).data('id');
                var status = $(this).prop('checked') ? 1 : 0;
                $.post('/admin/products-status', {
                    _token: '{{ csrf_token() }}',
                    product_id: product_id,
                    status: status
                }, function(d) {
                    reloadTable('#productTable');
                    showSuccess(d.message);
                }).fail(() => showError('Failed to update status'));
            });

            $(document).on('change', '.toggle-sidebar', function() {
                var product_id = $(this).data('id');
                var show_in_menu = $(this).prop('checked') ? 1 : 0;
                $.post('/admin/products-toggle-sidebar', {
                    _token: '{{ csrf_token() }}',
                    product_id: product_id,
                    show_in_menu: show_in_menu
                }, function(d) {
                    reloadTable('#productTable');
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
                form_data.append("short_description", $("#short_description").val());
                form_data.append("long_description", $(".summernote").summernote('code'));
                
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
                            reloadTable('#productTable');
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
                            reloadTable('#productTable');
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

            $("#contentContainer").on('click', '#EditBtn', function() {
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
                $("#short_description").val(data.short_description);
                $(".summernote").summernote('code', data.long_description);
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
                $("#addBtn").val('Create').html('Create');
                $("#cardTitle").text('Add new Product');
                currentProductId = null;
            }
        });
    </script>

@endsection