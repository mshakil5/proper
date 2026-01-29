@extends('admin.pages.master')
@section('title', 'Payment Credentials')
@section('content')

<div class="container-fluid" id="formContainer" style="display:none;">
    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0" id="cardTitle">Edit Credential</h4>
                </div>
                <div class="card-body">
                    <form id="editForm">
                        @csrf
                        <input type="hidden" id="id" name="id">
                        
                        <div class="mb-3">
                            <label class="form-label">Client ID</label>
                            <input type="text" class="form-control" id="client_id" name="client_id">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Client Secret</label>
                            <input type="text" class="form-control" id="client_secret" name="client_secret">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mode</label>
                            <select class="form-control" id="mode" name="mode" required>
                                <option value="sandbox">Sandbox</option>
                                <option value="live">Live</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" id="submitBtn" class="btn btn-primary">Update</button>
                    <button type="button" id="closeBtn" class="btn btn-secondary">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">Payment Credentials</h4>
        </div>
        <div class="card-body">
            <table id="credentialTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Gateway</th>
                        <th>Client ID</th>
                        <th>Mode</th>
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
    $('#credentialTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 25,
        ajax: "{{ route('credentials.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'gateway', name: 'gateway' },
            { data: 'client_id', name: 'client_id' },
            { data: 'mode', name: 'mode' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).on('click', '#EditBtn', function() {
        var id = $(this).attr('rid');
        $.get("/admin/credentials/" + id + "/edit", function(d) {
            $("#id").val(d.id);
            $("#client_id").val(d.client_id);
            $("#client_secret").val(d.client_secret);
            $("#mode").val(d.mode);
            $("#cardTitle").text('Edit ' + d.gateway + ' Credential');
            $("#formContainer").slideDown();
        });
    });

    $("#submitBtn").click(function() {
        var id = $("#id").val();
        var data = {
            client_id: $("#client_id").val(),
            client_secret: $("#client_secret").val(),
            mode: $("#mode").val()
        };

        $.ajax({
            url: "/admin/credentials/" + id,
            type: "PUT",
            data: data,
            success: function(d) {
                showSuccess(d.message);
                $("#formContainer").slideUp();
                $('#credentialTable').DataTable().ajax.reload();
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let firstError = Object.values(xhr.responseJSON.errors)[0][0];
                    showError(firstError);
                } else {
                    showError(xhr.responseJSON?.message ?? "Error!");
                }
            }
        });
    });

    $("#closeBtn").click(function() {
        $("#formContainer").slideUp();
    });
});
</script>
@endsection