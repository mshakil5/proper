@extends('admin.pages.master')
@section('title', 'Category')
@section('content')

    <div class="container-fluid" id="newBtnSection">
        <div class="row mb-3">
            <div class="col-auto">
                <button type="button" class="btn btn-primary" id="newBtn">
                    Add New Category
                </button>
            </div>
        </div>
    </div>

    <div class="container-fluid" id="addThisFormContainer">
        <div class="row justify-content-center">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1" id="cardTitle">Add New Category</h4>
                    </div>
                    <div class="card-body">
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" id="codeid" name="codeid">

                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Category Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control summernote" id="description" name="description"
                                        placeholder="Enter category description (optional)"></textarea>
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
        <ul class="nav nav-tabs mb-3" id="categoryTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="list-tab" data-bs-toggle="tab" href="#list" role="tab">Categories List</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="sort-tab" data-bs-toggle="tab" href="#sort" role="tab">Sort Categories</a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="list" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Categories</h4>
                    </div>
                    <div class="card-body">
                        <table id="categoryTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Show in Menu</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="sort" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Sort Categories</h4>
                        <small class="text-muted">Drag & drop rows to change order</small>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Name</th>
                                </tr>
                            </thead>
                            <tbody id="sortable">
                                @foreach ($categories as $category)
                                    <tr data-id="{{ $category->id }}">
                                        <td>{{ $category->sl }}</td>
                                        <td>{{ $category->name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <script>
        $(document).ready(function() {
            $("#sortable").sortable({
                placeholder: "ui-state-highlight",
                cursor: "grab",
                forcePlaceholderSize: true,
                opacity: 0.8,
                update: function(event, ui) {
                    var order = $(this).sortable('toArray', {
                        attribute: 'data-id'
                    });
                    $.ajax({
                        url: "{{ route('categories.updateOrder') }}",
                        method: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            order: order
                        },
                        success: function(res) {
                            showSuccess(res.message);
                            $("#sortable tr").each(function(index) {
                                $(this).find("td:first").text(index + 1);
                            });
                            reloadTable('#categoryTable');
                        },
                        error: function(xhr) {
                            showError(xhr.responseJSON?.message ?? "Something went wrong!");
                        }
                    });
                }
            });

            $('#categoryTable').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                ajax: "{{ route('allcategories') }}",
                columns: [{
                        data: 'sl',
                        name: 'sl',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
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

            $(document).on('change', '.toggle-status', function() {
                var category_id = $(this).data('id');
                var status = $(this).prop('checked') ? 1 : 0;
                $.post('/admin/categories-status', {
                    _token: '{{ csrf_token() }}',
                    category_id: category_id,
                    status: status
                }, function(d) {
                    reloadTable('#categoryTable');
                    showSuccess(d.message);
                }).fail(() => showError('Failed to update status'));
            });

            $(document).on('change', '.toggle-sidebar', function() {
                var category_id = $(this).data('id');
                var show_in_menu = $(this).prop('checked') ? 1 : 0;
                $.post('/admin/categories-toggle-sidebar', {
                    _token: '{{ csrf_token() }}',
                    category_id: category_id,
                    show_in_menu: show_in_menu
                }, function(d) {
                    reloadTable('#categoryTable');
                    showSuccess(d.message);
                }).fail(() => showError('Failed to update sidebar visibility'));
            });

            $("#addThisFormContainer").hide();
            $("#newBtn").click(function() {
                clearform();
                $("#addThisFormContainer").slideDown(300);
                $("#newBtn").hide();
            });
            $("#FormCloseBtn").click(function() {
                $("#addThisFormContainer").slideUp(300);
                setTimeout(() => {
                    $("#newBtn").show();
                }, 300);
            });

            var url = "{{ URL::to('/admin/categories') }}";
            var upurl = "{{ URL::to('/admin/categories-update') }}";

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("#addBtn").click(function() {
                var form_data = new FormData();
                form_data.append("name", $("#name").val());
                form_data.append("description", $(".summernote").summernote('code'));

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
                            reloadTable('#categoryTable');
                            clearform();
                        },
                        error: function(xhr) {
                            showError(xhr.responseJSON?.message ?? "Something went wrong!");
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
                            reloadTable('#categoryTable');
                            clearform();
                        },
                        error: function(xhr) {
                            showError(xhr.responseJSON?.message ?? "Something went wrong!");
                        }
                    });
                }
            });

            $("#contentContainer").on('click', '#EditBtn', function() {
                $("#cardTitle").text('Update this data');
                codeid = $(this).attr('rid');
                $.get(url + '/' + codeid + '/edit', {}, function(d) {
                    populateForm(d);
                });
            });

            function populateForm(data) {
                $("#name").val(data.name);
                $(".summernote").summernote('code', data.description);
                $("#codeid").val(data.id);
                $("#addBtn").val('Update').html('Update');
                $("#addThisFormContainer").show();
                $("#newBtn").hide();
            }

            function clearform() {
                $('#createThisForm')[0].reset();
                $("#addBtn").val('Create').html('Create');
                $("#cardTitle").text('Add new Category');
            }
        });
    </script>

@endsection