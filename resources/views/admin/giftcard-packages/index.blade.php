@extends('admin.pages.master')
@section('title', 'Gift Card Packages')
@section('content')

<div class="container-fluid mb-3">
    <button type="button" class="btn btn-primary" id="newBtn">Add Package</button>
</div>

<div class="container-fluid" id="formContainer" style="display:none;">
    <div class="row justify-content-center">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0" id="cardTitle">Add Package</h4>
                </div>
                <div class="card-body">
                    <form id="createForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="codeid" name="codeid">
                        
                        <div class="mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Amount (£) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="amount" name="amount" step="0.01">
                        </div>

                        <div class="mb-3 d-none">
                            <label class="form-label">Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*" onchange="previewImage(event, '#preview-image')">
                            <img id="preview-image" src="#" alt="" class="img-thumbnail rounded mt-3" style="max-width: 300px; display: none;">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description"></textarea>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" id="submitBtn" class="btn btn-primary">Create</button>
                    <button type="button" id="closeBtn" class="btn btn-secondary">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">Gift Card Packages</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
            <table id="packageTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Amount</th>
                        {{-- <th>Image</th> --}}
                        <th>Status</th>
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
    $('#packageTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 25,
        ajax: "{{ route('giftcard-packages.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'amount', name: 'amount', orderable: false, searchable: false },
            //{ data: 'image', name: 'image', orderable: false, searchable: false },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    $("#newBtn").click(function() {
        clearForm();
        $("#formContainer").slideDown();
        $("#newBtn").hide();
    });

    $("#closeBtn").click(function() {
        $("#formContainer").slideUp();
        $("#newBtn").show();
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("#submitBtn").click(function() {
        var form_data = new FormData(document.getElementById('createForm'));
        var codeid = $("#codeid").val();
        var url = codeid ? "/admin/giftcard-packages/" + codeid : "{{ route('giftcard-packages.store') }}";
        var method = "POST";

        if(codeid) form_data.append('_method', 'PUT');

        $.ajax({
            url: url,
            type: method,
            data: form_data,
            contentType: false,
            processData: false,
            success: function(d) {
                showSuccess(d.message);
                $("#formContainer").slideUp();
                $("#newBtn").show();
                $('#packageTable').DataTable().ajax.reload();
                clearForm();
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

    $(document).on('click', '#EditBtn', function() {
        var id = $(this).attr('rid');
        $.get("/admin/giftcard-packages/" + id + "/edit", function(d) {
            $("#codeid").val(d.id);
            $("#name").val(d.name);
            $("#amount").val(d.amount);
            $("#description").val(d.description);
            
            var imagePreview = document.getElementById('preview-image');
            if (d.image) {
                imagePreview.src = d.image;
                imagePreview.style.display = 'block';
            } else {
                imagePreview.style.display = 'none';
            }
            
            $("#cardTitle").text('Update Package');
            $("#submitBtn").html('Update');
            $("#formContainer").slideDown();
            $("#newBtn").hide();
        });
    });

    $(document).on('change', '.toggle-status', function() {
        var id = $(this).data('id');
        var is_active = $(this).prop('checked') ? 1 : 0;
        $.post('/admin/giftcard-packages-status', {
            _token: '{{ csrf_token() }}',
            id: id,
            is_active: is_active
        }, function(d) {
            showSuccess(d.message);
            $('#packageTable').DataTable().ajax.reload();
        }).fail(() => showError('Failed to update status'));
    });

    function clearForm() {
        $('#createForm')[0].reset();
        $("#codeid").val('');
        $("#cardTitle").text('Add Package');
        $("#submitBtn").html('Create');
        document.getElementById('preview-image').style.display = 'none';
    }

    function previewImage(event, selector) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.querySelector(selector);
            output.src = reader.result;
            output.style.display = 'block';
        }
        reader.readAsDataURL(event.target.files[0]);
    }
});
</script>
@endsection