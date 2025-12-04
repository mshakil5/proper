@extends('admin.master')

@section('content')
<section class="content" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-2">
                <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new</button>
            </div>
        </div>
    </div>
</section>

<section class="content pt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-10">
                <form id="createThisForm">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title" id="cardTitle">Add new User</h3>
                        </div>
                        <div class="card-body">
                            <input type="hidden" id="id" name="id">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name" autofocus required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Phone <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="phone" name="phone" required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Confirm Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" id="addBtn" class="btn btn-secondary" value="Create">Create</button>
                            <button type="button" id="FormCloseBtn" class="btn btn-default">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<section class="content" id="contentContainer">
    <div class="container-fluid">
        <div class="card card-secondary">
            <div class="card-header"><h3 class="card-title">Users</h3></div>
            <div class="card-body">
                <table id="userTable" class="table table-striped">
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
</section>
@endsection

@section('script')
<script>
$(document).ready(function () {
    $("#addThisFormContainer").hide();

    $("#newBtn").click(function(){
        clearForm();
        $("#newBtnSection").hide();
        $("#addThisFormContainer").show();
    });

    $("#FormCloseBtn").click(function(){
        clearForm();
        $("#addThisFormContainer").hide();
        $("#newBtnSection").show();
    });

    function clearForm(){
        $('#createThisForm')[0].reset();
        $("#cardTitle").text('Add new User');
        $("#addBtn").val('Create').html('Create');
        $("#addThisFormContainer").hide();  // hide the form
        $("#newBtnSection").show(); 
    }

    $.ajaxSetup({ headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')} });

    // Initialize Yajra DataTable
    var table = $('#userTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('user.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable:false, searchable:false },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'phone', name: 'phone' },
            { data: 'status', name: 'status', orderable:false, searchable:false },
            { data: 'action', name: 'action', orderable:false, searchable:false }
        ]
    });

    // Create & Update
    $("#createThisForm").on('submit', function(e){
        e.preventDefault();
        var actionUrl = $("#addBtn").val() === 'Create' ? "{{ route('user.store') }}" : "{{ route('user.update') }}";
        $.ajax({
            url: actionUrl,
            type: 'POST',
            data: $(this).serialize(),
            success: function(res){
                clearForm();
                table.ajax.reload(null, false);
                success(res.message);
            },
            error: function(xhr){
                if(xhr.responseJSON && xhr.responseJSON.errors)
                    error(Object.values(xhr.responseJSON.errors)[0][0]);
                else error();
            }
        });
    });

    // Edit
    $('#userTable').on('click', '.edit', function(){
        var id = $(this).data('id');
        $.get("{{ url('/admin/user') }}/"+id+"/edit", function(user){
            $("#name").val(user.name);
            $("#email").val(user.email);
            $("#phone").val(user.phone);
            $("#id").val(user.id);
            $("#password, #password_confirmation").prop('required', false);
            $("#addBtn").val('Update').html('Update');
            $("#cardTitle").text('Update User');
            $("#addThisFormContainer").show();
            $("#newBtnSection").hide();
        });
    });

    // Delete
    $('#userTable').on('click', '.delete', function(){
        if(!confirm('Are you sure?')) return;
        var id = $(this).data('id');
        $.get("{{ url('/admin/user') }}/"+id+"/delete", function(res){
            table.ajax.reload(null, false);
        });
    });

    // Status toggle
    $('#userTable').on('change', '.toggle-status', function(){
        var id = $(this).data('id');
        $.post("{{ route('user.status') }}", {id:id}, function(res){
            success(res.message);
            table.ajax.reload(null, false);
        });
    });
});
</script>
@endsection