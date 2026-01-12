@extends('admin.pages.master')
@section('title', 'Clients')
@section('content')
    <div class="container-fluid" id="newBtnSection">
        <div class="row mb-3">
            <div class="col-auto">
                <button class="btn btn-primary" id="newBtn">Add New Client</button>
            </div>
        </div>
    </div>

    <div class="container-fluid" id="addThisFormContainer" style="display:none;">
        <div class="row justify-content-center">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <h4 id="cardTitle">Add New Client</h4>
                    </div>
                    <div class="card-body">
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" id="codeid" name="id">
                            <div class="mb-3">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="text" id="phone" name="phone" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password <span class="text-danger" id="passwordRequired">*</span></label>
                                <input type="password" id="password" name="password" class="form-control">
                                <small class="form-text text-muted">Leave empty to keep current password (edit mode only)</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm Password <span class="text-danger" id="confirmRequired">*</span></label>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                            </div>
                            <div class="mb-3 text-end">
                                <button type="button" id="addBtn" class="btn btn-primary" value="Create">Create</button>
                                <button type="button" id="FormCloseBtn" class="btn btn-light">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid" id="contentContainer">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">All Clients</h4>
            </div>
            <div class="card-body">
                <table id="clientTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
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
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var table = $('#clientTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('client.index') }}",
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
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
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

            $('#newBtn').click(function() {
                $('#createThisForm')[0].reset();
                $('#codeid').val('');
                $('#cardTitle').text('Add New Client');
                $('#addBtn').val('Create').text('Create');
                $('#passwordRequired').show();
                $('#confirmRequired').show();
                $('#password').prop('required', true);
                $('#password_confirmation').prop('required', true);
                $('small').hide();
                $('#addThisFormContainer').show(300);
                $('#newBtn').hide();
            });

            $('#FormCloseBtn').click(function() {
                $('#addThisFormContainer').hide(200);
                $('#newBtn').show(100);
                $('#createThisForm')[0].reset();
            });

            // Create / Update
            $('#addBtn').click(function() {
                var btn = this;
                var url = $(btn).val() === 'Create' ? "{{ route('client.store') }}" :
                    "{{ route('client.update') }}";
                var form = document.getElementById('createThisForm');
                var fd = new FormData(form);
                if ($(btn).val() !== 'Create') fd.append('id', $('#codeid').val());

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        showSuccess(res.message);
                        $('#addThisFormContainer').hide();
                        $('#newBtn').show();
                        table.ajax.reload(null, false);
                        $('#createThisForm')[0].reset();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON) {
                            let first = Object.values(xhr.responseJSON.errors)[0][0];
                            showError(first);
                        } else {
                            showError(xhr.responseJSON?.message ?? 'Something went wrong');
                        }
                    }
                });
            });

            // Edit
            $(document).on('click', '.EditBtn', function() {
                var id = $(this).data('id');
                $.get("{{ url('/admin/client') }}/" + id + "/edit", {}, function(res) {
                    $('#codeid').val(res.id);
                    $('#name').val(res.name);
                    $('#email').val(res.email);
                    $('#phone').val(res.phone);
                    $('#password').val('');
                    $('#password_confirmation').val('');
                    $('#password').prop('required', false);
                    $('#password_confirmation').prop('required', false);
                    $('#passwordRequired').hide();
                    $('#confirmRequired').hide();
                    $('small').show();
                    $('#cardTitle').text('Update Client');
                    $('#addBtn').val('Update').text('Update');
                    $('#addThisFormContainer').show(300);
                    $('#newBtn').hide();
                });
            });

            // Delete
            $(document).on('click', '.deleteBtn', function() {
                if (!confirm('Sure?')) return;
                $.ajax({
                    url: $(this).data('delete-url'),
                    type: 'DELETE',
                    success: function(res) {
                        showSuccess(res.message);
                        table.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        showError(xhr.responseJSON?.message ?? 'Failed');
                    }
                });
            });

            // Status toggle
            $(document).on('change', '.toggle-status', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: "{{ route('client.toggleStatus') }}",
                    type: 'POST',
                    data: {id: id},
                    success: function(res) {
                        showSuccess(res.message);
                        table.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        showError(xhr.responseJSON?.message ?? 'Failed');
                    }
                });
            });
        });
    </script>
@endsection