@extends('admin.master')

@section('content')
<!-- Add new button -->
<section class="content" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-2">
                <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new</button>
            </div>
        </div>
    </div>
</section>

<!-- Form -->
<section class="content mt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-8">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Add new tag</h3>
                    </div>
                    <div class="card-body">
                        <form id="createThisForm">@csrf
                            <input type="hidden" id="codeid" name="codeid">
                            <div class="form-group">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter tag name" required>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <button type="submit" id="addBtn" class="btn btn-secondary" value="Create">Create</button>
                        <button type="submit" id="FormCloseBtn" class="btn btn-default">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Table -->
<section class="content" id="contentContainer">
    <div class="container-fluid">
        <div class="card card-secondary">
            <div class="card-header"><h3 class="card-title">All Tags</h3></div>
            <div class="card-body">
                <table id="example1" class="table cell-border table-striped">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Name</th>
                            <th>Slug</th>
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

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });

    $("#addThisFormContainer").hide();

    $("#newBtn").click(function(){
        clearform();
        $("#newBtn").hide(100);
        $("#addThisFormContainer").show(300);
    });

    $("#FormCloseBtn").click(function(){
        $("#addThisFormContainer").hide(200);
        $("#newBtn").show(100);
        clearform();
    });

    let url = "{{ url('/admin/tags') }}";
    let upurl = "{{ url('/admin/tags/update') }}";

    // Create / Update
    $("#addBtn").click(function(){
        let form_data = new FormData();
        form_data.append("name", $("#name").val());

        if($(this).val() == 'Create'){
            $.ajax({
                url: url,
                type: "POST",
                contentType: false,
                processData: false,
                data: form_data,
                success: function(res){
                    clearform();
                    success(res.message);
                    reloadTable();
                },
                error: function(xhr){
                    error(Object.values(xhr.responseJSON.errors)[0][0]);
                }
            });
        }else{
            form_data.append("codeid", $("#codeid").val());
            $.ajax({
                url: upurl,
                type: "POST",
                contentType: false,
                processData: false,
                data: form_data,
                success: function(res){
                    clearform();
                    success(res.message);
                    reloadTable();
                },
                error: function(xhr){
                    error(Object.values(xhr.responseJSON.errors)[0][0]);
                }
            });
        }
    });

    // Edit
    $("#contentContainer").on('click','.edit',function(){
        codeid = $(this).data('id');
        $.get(url+'/'+codeid+'/edit',{},function(d){
            populateForm(d);
        });
    });

    function populateForm(data){
        $("#name").val(data.name);
        $("#codeid").val(data.id);
        $("#addBtn").val('Update').html('Update');
        $("#cardTitle").text('Update tag');
        $("#addThisFormContainer").show(300);
        $("#newBtn").hide(100);
    }

    function clearform(){
        $('#createThisForm')[0].reset();
        $("#addBtn").val('Create').html('Create');
        $("#cardTitle").text('Add new tag');
        $("#addThisFormContainer").slideUp(200);
        $("#newBtn").slideDown(200);
    }

    // Toggle status
    $(document).on('change','.toggle-status',function(){
        var id=$(this).data('id');
        var status=$(this).prop('checked')?1:0;
        $.post("{{ url('/admin/tags/status') }}",{id:id,status:status,_token:"{{csrf_token()}}"},function(res){
            success(res.message);
            reloadTable();
        });
    });

    // Delete
    $("#contentContainer").on('click','.delete',function(){
        if(!confirm('Delete this tag?')) return;
        codeid=$(this).data('id');
        $.get(url+'/'+codeid+'/delete',{},function(res){
            success(res.message);
            reloadTable();
        });
    });

    // DataTable
    var table=$('#example1').DataTable({
        processing:true,serverSide:true,
        ajax:"{{ route('tags.index') }}",
        columns:[
            {data:'DT_RowIndex',orderable:false,searchable:false},
            {data:'name',name:'name'},
            {data:'slug',name:'slug'},
            {data:'status',name:'status',orderable:false,searchable:false},
            {data:'action',name:'action',orderable:false,searchable:false},
        ]
    });

    function reloadTable(){table.ajax.reload(null,false);}
});
</script>
@endsection
