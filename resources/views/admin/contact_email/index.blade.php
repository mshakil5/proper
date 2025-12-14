@extends('admin.pages.master')
@section('title', 'Contact Emails')
@section('content')
    <div class="container-fluid" id="newBtnSection">
        <div class="row mb-3">
            <div class="col-auto">
                <button type="button" class="btn btn-primary" id="newBtn">
                    Add New Email
                </button>
            </div>
        </div>
    </div>

    <div class="container-fluid" id="addThisFormContainer">
        <div class="row justify-content-center">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1" id="cardTitle">Add New Contact Email</h4>
                    </div>
                    <div class="card-body">
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" id="codeid" name="codeid">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter email">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email Holder <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="email_holder" name="email_holder" placeholder="Enter holder name">
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
                <h4 class="card-title mb-0">Contact Emails List</h4>
            </div>
            <div class="card-body">
                <table id="contactEmailTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Email</th>
                            <th>Email Holder</th>
                            <th>Created At</th>
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
            $('#contactEmailTable').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                ajax: "{{ route('contactemail.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'email_holder',
                        name: 'email_holder'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
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

            var url = "{{ URL::to('/admin/contact-email') }}";
            var upurl = "{{ URL::to('/admin/contact-email-update') }}";

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("#addBtn").click(function() {
                var form_data = new FormData();
                form_data.append("email", $("#email").val());
                form_data.append("email_holder", $("#email_holder").val());

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
                            reloadTable('#contactEmailTable');
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
                            reloadTable('#contactEmailTable');
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

            // Delete button handler (using your existing deleteBtn pattern)
            $(document).on('click', '.deleteBtn', function() {
                if(!confirm('Are you sure you want to delete this contact email?')) return;
                
                var deleteUrl = $(this).data('delete-url');
                var method = $(this).data('method');
                var table = $(this).data('table');
                
                $.ajax({
                    url: deleteUrl,
                    type: method,
                    success: function(d) {
                        showSuccess(d.message);
                        reloadTable(table);
                    },
                    error: function(xhr) {
                        showError(xhr.responseJSON?.message ?? "Something went wrong!");
                    }
                });
            });

            function populateForm(data) {
                $("#email").val(data.email);
                $("#email_holder").val(data.email_holder);
                $("#codeid").val(data.id);
                $("#addBtn").val('Update').html('Update');
                $("#addThisFormContainer").show();
                $("#newBtn").hide();
            }

            function clearform() {
                $('#createThisForm')[0].reset();
                $("#addBtn").val('Create').html('Create');
                $("#cardTitle").text('Add new Contact Email');
            }
        });
    </script>
@endsection