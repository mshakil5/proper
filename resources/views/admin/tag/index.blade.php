@extends('admin.pages.master')
@section('title', 'Tags')
@section('content')
    <div class="container-fluid" id="newBtnSection">
        <div class="row mb-3">
            <div class="col-auto">
                <button type="button" class="btn btn-primary" id="newBtn">
                    Add New Tag
                </button>
            </div>
        </div>
    </div>

    <div class="container-fluid" id="addThisFormContainer">
        <div class="row justify-content-center">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1" id="cardTitle">Add New Tag</h4>
                    </div>
                    <div class="card-body">
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" id="codeid" name="codeid">

                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Tag Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name">
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
                <h4 class="card-title mb-0">Tags List</h4>
            </div>
            <div class="card-body">
                <table id="tagTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Name</th>
                            <th>Status</th>
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
        $(document).ready(function() {
            $('#tagTable').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                ajax: "{{ route('alltags') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
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
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $(document).on('change', '.toggle-status', function() {
                var tag_id = $(this).data('id');
                var status = $(this).prop('checked') ? 1 : 0;
                $.post('/admin/tags-status', {
                    _token: '{{ csrf_token() }}',
                    tag_id: tag_id,
                    status: status
                }, function(d) {
                    reloadTable('#tagTable');
                    showSuccess(d.message);
                }).fail(() => showError('Failed to update status'));
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

            var url = "{{ URL::to('/admin/tags') }}";
            var upurl = "{{ URL::to('/admin/tags-update') }}";

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("#addBtn").click(function() {
                var form_data = new FormData();
                form_data.append("name", $("#name").val());

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
                            reloadTable('#tagTable');
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
                            reloadTable('#tagTable');
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
                $("#codeid").val(data.id);
                $("#addBtn").val('Update').html('Update');
                $("#addThisFormContainer").show();
                $("#newBtn").hide();
            }

            function clearform() {
                $('#createThisForm')[0].reset();
                $("#addBtn").val('Create').html('Create');
                $("#cardTitle").text('Add new Tag');
            }
        });
    </script>

@endsection