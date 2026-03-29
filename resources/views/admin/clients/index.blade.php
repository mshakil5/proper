@extends('admin.pages.master')
@section('title', 'Clients')
@section('content')
    <div class="container-fluid" id="newBtnSection">
        <div class="row mb-3">
            <div class="col-auto">
                <button class="btn btn-primary" id="newBtn">Add New Client</button>
                <button class="btn btn-success" id="exportBtn"><i class="ri-download-line"></i> Export CSV</button>
                <button class="btn btn-info" id="importBtn"><i class="ri-upload-cloud-line"></i> Import CSV</button>
            </div>
        </div>
    </div>

    <input type="file" id="importFile" accept=".csv" style="display:none;">

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
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" id="first_name" name="first_name" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" id="last_name" name="last_name" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" id="email" name="email" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">Phone <span class="text-danger">*</span></label>
                                        <input type="text" id="phone" name="phone" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">Date of Birth</label>
                                        <input type="date" id="dob" name="dob" class="form-control">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">Postal Code</label>
                                        <input type="text" id="postcode" name="postcode" class="form-control">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">Address Line 1</label>
                                        <input type="text" id="address_1" name="address_1" class="form-control">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">Street</label>
                                        <input type="text" id="street" name="street" class="form-control">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">City</label>
                                        <input type="text" id="city" name="city" class="form-control">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">Address Line 2</label>
                                        <input type="text" id="address_2" name="address_2" class="form-control">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">Password <span class="text-danger" id="passwordRequired">*</span></label>
                                        <input type="password" id="password" name="password" class="form-control">
                                        <small class="form-text text-muted">Leave empty to keep current password (edit mode only)</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">Confirm Password <span class="text-danger" id="confirmRequired">*</span></label>
                                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3 text-end">
                                        <button type="button" id="addBtn" class="btn btn-primary" value="Create">Create</button>
                                        <button type="button" id="FormCloseBtn" class="btn btn-light">Cancel</button>
                                    </div>
                                </div>
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
                            <th>Orders</th>
                            <th>Gift Cards</th>
                            <th>Points</th>
                            <th>Subscription</th>
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
                    { data: 'name', name: 'name' },
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
                        data: 'orders',
                        name: 'orders',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'gift_cards',
                        name: 'gift_cards',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'points',
                        name: 'points',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'subscription',
                        name: 'subscription',
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
                    $('#first_name').val(res.first_name);
                    $('#last_name').val(res.last_name);
                    $('#email').val(res.email);
                    $('#phone').val(res.phone);
                    $('#dob').val(res.dob);
                    $('#postcode').val(res.postcode);
                    $('#address_1').val(res.address_1);
                    $('#street').val(res.street);
                    $('#city').val(res.city);
                    $('#address_2').val(res.address_2);
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
                    pageTop();
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

            // Export CSV
            $('#exportBtn').click(function() {
                window.location.href = "{{ route('client.export') }}";
            });

            // Import CSV
            $('#importBtn').click(function() {
                $('#importFile').click();
            });

            $('#importFile').change(function() {
                var file = this.files[0];
                if (!file) return;

                var fd = new FormData();
                fd.append('file', file);

                $.ajax({
                    url: "{{ route('client.import') }}",
                    type: 'POST',
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        showSuccess(res.message);
                        if (res.errors.length > 0) {
                            showError(res.errors.join(', '));
                        }
                        table.ajax.reload(null, false);
                        $('#importFile').val('');
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON) {
                            showError(xhr.responseJSON.errors.file[0]);
                        } else {
                            showError(xhr.responseJSON?.message ?? 'Import failed');
                        }
                        $('#importFile').val('');
                    }
                });
            });
        });
    </script>
@endsection