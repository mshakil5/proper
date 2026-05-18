@extends('admin.pages.master')
@section('title', 'Blocked Customers')

@section('content')

    <div class="container-fluid mb-3">
        <button type="button" class="btn btn-primary" id="newBtn">Add Block</button>
    </div>

    <div class="container-fluid" id="formContainer" style="display:none;">
        <div class="row justify-content-center">
            <div class="col-xl-6">
                <div class="card">

                    <div class="card-header">
                        <h4 class="card-title mb-0" id="cardTitle">Add Blocked Customer</h4>
                    </div>

                    <div class="card-body">
                        <form id="createForm">
                            @csrf
                            <input type="hidden" id="codeid">

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="text" class="form-control" id="email">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Domain</label>
                                <input type="text" class="form-control" id="domain">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Reason</label>
                                <textarea class="form-control" id="reason"></textarea>
                            </div>

                        </form>
                    </div>

                    <div class="card-footer text-end">
                        <button type="button" id="submitBtn" class="btn btn-primary">Create</button>
                        <button type="button" id="closeBtn" class="btn btn-secondary">Cancel</button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card">

            <div class="card-header">
                <h4 class="card-title mb-0">Blocked Customers</h4>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                <table id="blockedTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Email</th>
                            <th>Domain</th>
                            <th>Phone</th>
                            <th>Reason</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
                </div>
            </div>

        </div>
    </div>

@endsection

@section('script')
    <script>
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let table = $('#blockedTable').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                ajax: "{{ route('admin.blocked.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'domain',
                        name: 'domain'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'reason',
                        name: 'reason',
                        orderable: false
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [0, 'desc']
                ]
            });

            $('#newBtn').click(function() {
                clearForm();
                $('#formContainer').slideDown();
                $('#newBtn').hide();
            });

            $('#closeBtn').click(function() {
                $('#formContainer').slideUp();
                $('#newBtn').show();
            });

            $('#submitBtn').click(function() {

                let id = $('#codeid').val();

                $.ajax({
                    url: id ?
                        '/admin/blocked-customers/' + id :
                        "{{ route('admin.blocked.store') }}",
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        _method: id ? 'PUT' : 'POST',
                        email: $('#email').val(),
                        domain: $('#domain').val(),
                        phone: $('#phone').val(),
                        reason: $('#reason').val()
                    },
                    success: function(res) {
                        showSuccess(res.message);
                        table.ajax.reload();

                        $('#formContainer').slideUp();
                        $('#newBtn').show();
                        clearForm();
                    },
                    error: function() {
                        showError("Something went wrong!");
                    }
                });

            });

            $(document).on('click', '.editBtn', function() {

                let id = $(this).data('id');

                $.get('/admin/blocked-customers/' + id + '/edit', function(d) {

                    $('#codeid').val(d.id);
                    $('#email').val(d.email);
                    $('#domain').val(d.domain);
                    $('#phone').val(d.phone);
                    $('#reason').val(d.reason);

                    $('#cardTitle').text('Update Blocked Customer');
                    $('#submitBtn').text('Update');

                    $('#formContainer').slideDown();
                    $('#newBtn').hide();

                });

            });

            function clearForm() {
                $('#createForm')[0].reset();
                $('#codeid').val('');
                $('#cardTitle').text('Add Blocked Customer');
                $('#submitBtn').text('Create');
            }

        });
    </script>
@endsection